<?php

namespace App\Traits;

use App\Mail\SendMail;
use App\Models\EmailTemplate;
use App\Models\SocialMedia;
use App\Repositories\EmailTemplatesRepository;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

trait EmailTemplateTrait
{
    use FileManagerTrait;
    protected function textVariableFormat(
        $value, $userName = null, $adminName = null,$vendorName = null ,$shopName = null,$shopId = null,
        $deliveryManName = null,$orderId = null ,$emailId = null)
    {
        $data = $value;
        if ($data) {
            $data = $userName ? str_replace("{userName}", $userName, $data) : $data;
            $data = $vendorName ? str_replace("{vendorName}", $vendorName, $data) : $data;
            $data = $adminName ? str_replace("{adminName}", $adminName, $data) : $data;
            $data = $shopName ? str_replace("{shopName}", $shopName, $data) : $data;
            $data = $shopName ? str_replace("{shopId}", $shopId, $data) : $data;
            $data = $deliveryManName ? str_replace("{deliveryManName}", $deliveryManName, $data) : $data;
            $data = $orderId ? str_replace("{orderId}", $orderId, $data) : $data;
            $data = $emailId ? str_replace("{emailId}", $emailId, $data) : $data;
        }
        return $data;
    }

    protected function sendingMail(
        string $sendMailTo,
        string $userType,
        string $templateName,
        array  $data = null
    ): void {
        Log::info("EmailTemplateTrait: fetching template '{$templateName}' for userType '{$userType}'");

        $template = EmailTemplate::with('translationCurrentLanguage')
            ->where([
                'user_type'    => $userType,
                'template_name'=> $templateName,
            ])->first();

        if (! $template) {
            Log::warning("EmailTemplateTrait: no template found for '{$templateName}' / '{$userType}'");
            return;
        }

        // apply translations
        foreach ($template->translationCurrentLanguage ?? [] as $translate) {
            if ($translate->key === 'title')           $template->title           = $translate->value;
            if ($translate->key === 'body')            $template->body            = $translate->value;
            if ($translate->key === 'footer_text')     $template->footer_text     = $translate->value;
            if ($translate->key === 'copyright_text')  $template->copyright_text  = $translate->value;
            if ($translate->key === 'button_name')     $template->button_name     = $translate->value;
        }

        // fetch active social links
        $socialMedia = SocialMedia::where('status', 1)->get();

        // replace variables in title/body
        $template->body  = $this->textVariableFormat(
            $template->body,
            $data['userName']        ?? null,
            $data['adminName']       ?? null,
            $data['vendorName']      ?? null,
            $data['shopName']        ?? null,
            $data['shopId']          ?? null,
            $data['deliveryManName'] ?? null,
            $data['orderId']         ?? null,
            $data['emailId']         ?? null,
        );
        $template->title = $this->textVariableFormat(
            $template->title,
            $data['userName']        ?? null,
            $data['adminName']       ?? null,
            $data['vendorName']      ?? null,
            $data['shopName']        ?? null,
            null,
            null,
            $data['orderId']         ?? null
        );

        // mark that an email will be sent
        $data['send-mail'] = true;

        // only send if template is active
        if ((int)$template->status !== 1) {
            Log::warning("EmailTemplateTrait: template '{$templateName}' is disabled, skipping send");
        } else {
            Log::info("EmailTemplateTrait: about to send Mail to {$sendMailTo}", [
                'template' => $templateName,
                'data'     => $data,
            ]);

            try {
                Mail::to($sendMailTo)
                    ->send(new SendMail($data, $template, $socialMedia));

                $failures = Mail::failures();
                if (! empty($failures)) {
                    Log::error("EmailTemplateTrait: Mail failures for {$sendMailTo}", $failures);
                } else {
                    Log::info("EmailTemplateTrait: Mail sent successfully to {$sendMailTo}");
                }
            } catch (\Exception $e) {
                Log::error("EmailTemplateTrait: Exception sendingMail to {$sendMailTo} – " . $e->getMessage(), [
                    'exception' => $e
                ]);
            }
        }

        // clean up attachment if provided
        if (! empty($data['attachmentPath'])) {
            try {
                unlink($data['attachmentPath']);
            } catch (\Exception $e) {
                Log::warning("EmailTemplateTrait: failed to unlink attachment at {$data['attachmentPath']} – " . $e->getMessage());
            }
        }
    }

    public function getEmailTemplateDataForUpdate($userType): void
    {
        $emailTemplates = EmailTemplate::where(['user_type' => $userType])->get();
        $emailTemplateArray = (new EmailTemplateService)->getEmailTemplateData(userType: $userType);
        foreach ($emailTemplateArray as $value) {
            $checkKey = $emailTemplates->where('template_name', $value)->first();
            if ($checkKey === null) {
                $hideField = (new EmailTemplateService)->getHiddenField(userType: $userType, templateName: $value);
                $title = (new EmailTemplateService)->getTitleData(userType: $userType, templateName: $value);
                $body = (new EmailTemplateService)->getBodyData(userType: $userType, templateName: $value);
                $addData = (new EmailTemplateService)->getAddData(userType: $userType, templateName: $value, hideField: $hideField, title: $title, body: $body);
                EmailTemplate::create($addData);
            }
        }
        foreach ($emailTemplates as $value) {
            if (!in_array($value['template_name'], $emailTemplateArray)) {
                EmailTemplate::find($value['id'])->delete();
            }
        }
    }
}
