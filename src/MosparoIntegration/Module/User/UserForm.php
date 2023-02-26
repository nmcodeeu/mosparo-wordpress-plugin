<?php

namespace MosparoIntegration\Module\User;

use MosparoIntegration\Helper\FrontendHelper;
use MosparoIntegration\Helper\VerificationHelper;
use WP_Error;

class UserForm
{
    private static $instance;

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    public function registerHooks()
    {
        add_action('login_form', [$this, 'displayMosparoField'], 10, 1);
        add_action('lostpassword_form', [$this, 'displayMosparoField'], 10, 1);
        add_action('register_form', [$this, 'displayMosparoField'], 10, 1);
        add_filter('wp_authenticate_user', [$this, 'verifyLoginForm'], 1000);
        add_action('lostpassword_post', [$this, 'verifyLostPasswordForm']);
        add_action('register_post', [$this, 'verifyRegisterForm'], 10, 3);
    }

    public function displayMosparoField()
    {
        $frontendHelper = FrontendHelper::getInstance();
        echo $frontendHelper->generateField();
    }

    public function verifyLoginForm($user)
    {
        $errors = new WP_Error();

        $submitToken = trim(sanitize_text_field($_REQUEST['_mosparo_submitToken'] ?? ''));
        $validationToken = trim(sanitize_text_field($_REQUEST['_mosparo_validationToken'] ?? ''));

        $formData = apply_filters('mosparo_integration_user_login_form_data', [
            'log' => sanitize_user($_REQUEST['log']),
        ]);

        $verificationHelper = VerificationHelper::getInstance();
        $verificationResult = $verificationHelper->verifySubmission($submitToken, $validationToken, $formData);
        if ($verificationResult === null) {
            $errors->add(
                'mosparo_integration_general_error',
                sprintf(__('A general error occurred: %s', 'mosparo-integration'), $verificationHelper->getLastException()->getMessage())
            );
            return $errors;
        }

        // Confirm that all required fields were verified
        $verifiedFields = array_keys($verificationResult->getVerifiedFields());
        $fieldDifference = array_diff(['log'], $verifiedFields);

        if (!$verificationResult->isSubmittable() || !empty($fieldDifference)) {
            $errors->add(
                'mosparo_integration_spam_error',
                __('Verification failed which means the form contains spam.', 'mosparo-integration')
            );

            return $errors;
        }

        return $user;
    }

    public function verifyLostPasswordForm(WP_Error $errors)
    {
        $submitToken = trim(sanitize_text_field($_REQUEST['_mosparo_submitToken'] ?? ''));
        $validationToken = trim(sanitize_text_field($_REQUEST['_mosparo_validationToken'] ?? ''));

        $formData = apply_filters('mosparo_integration_user_lost_password_form_data', [
            'user_login' => sanitize_text_field($_REQUEST['user_login']),
        ]);

        $verificationHelper = VerificationHelper::getInstance();
        $verificationResult = $verificationHelper->verifySubmission($submitToken, $validationToken, $formData);
        if ($verificationResult === null) {
            $errors->add(
                'mosparo_integration_general_error',
                sprintf(__('A general error occurred: %s', 'mosparo-integration'), $verificationHelper->getLastException()->getMessage())
            );
            return;
        }

        // Confirm that all required fields were verified
        $verifiedFields = array_keys($verificationResult->getVerifiedFields());
        $fieldDifference = array_diff(['user_login'], $verifiedFields);

        if (!$verificationResult->isSubmittable() || !empty($fieldDifference)) {
            $errors->add(
                'mosparo_integration_spam_error',
                __('Verification failed which means the form contains spam.', 'mosparo-integration')
            );
        }
    }

    public function verifyRegisterForm($username, $email, WP_Error $errors)
    {
        $submitToken = trim(sanitize_text_field($_REQUEST['_mosparo_submitToken'] ?? ''));
        $validationToken = trim(sanitize_text_field($_REQUEST['_mosparo_validationToken'] ?? ''));

        $formData = apply_filters('mosparo_integration_user_register_form_data', [
            'user_login' => sanitize_text_field($_REQUEST['user_login']),
            'user_email' => sanitize_email($_REQUEST['user_email']),
        ]);

        $verificationHelper = VerificationHelper::getInstance();
        $verificationResult = $verificationHelper->verifySubmission($submitToken, $validationToken, $formData);
        if ($verificationResult === null) {
            $errors->add(
                'mosparo_integration_general_error',
                sprintf(__('A general error occurred: %s', 'mosparo-integration'), $verificationHelper->getLastException()->getMessage())
            );
            return;
        }

        // Confirm that all required fields were verified
        $verifiedFields = array_keys($verificationResult->getVerifiedFields());
        $fieldDifference = array_diff(['user_login', 'user_email2'], $verifiedFields);

        if (!$verificationResult->isSubmittable() || !empty($fieldDifference)) {
            $errors->add(
                'mosparo_integration_spam_error',
                __('Verification failed which means the form contains spam.', 'mosparo-integration')
            );
        }
    }
}