<?php

namespace OTP\Objects;

interface MoITemplate
{
    public function build($template,$templateType,$message,$otp_type,$from_both);
    public function parse($template,$message,$otp_type,$from_both);
    public function getDefaults($templates);
    public function showPreview();
    public function savePopup();
    public static function instance();
    public function getTemplateKey();
    public function getTemplateEditorId();
}