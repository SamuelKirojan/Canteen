<?php
require_once APP_ROOT . '/app/core/Controller.php';

class ContactController extends Controller {
    public function submit(): void {
        header('Content-Type: application/json');
        $vendor = APP_ROOT . '/public/assets/vendor/php-email-form/php-email-form.php';
        if (!file_exists($vendor)) {
            echo json_encode(['status' => 'error', 'message' => 'Email library missing']);
            return;
        }
        include $vendor;
        try {
            $contact = new PHP_Email_Form;
            $contact->ajax = true;
            $contact->to = 'contact@example.com'; // TODO: update
            $contact->from_name = $_POST['name'] ?? '';
            $contact->from_email = $_POST['email'] ?? '';
            $contact->subject = $_POST['subject'] ?? 'Website Contact';
            $contact->add_message($_POST['name'] ?? '', 'From');
            $contact->add_message($_POST['email'] ?? '', 'Email');
            $contact->add_message($_POST['message'] ?? '', 'Message', 10);
            echo $contact->send();
        } catch (Throwable $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
