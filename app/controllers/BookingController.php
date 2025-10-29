<?php
require_once APP_ROOT . '/app/core/Controller.php';

class BookingController extends Controller {
    public function submit(): void {
        header('Content-Type: application/json');
        $vendor = APP_ROOT . '/public/assets/vendor/php-email-form/php-email-form.php';
        if (!file_exists($vendor)) {
            echo json_encode(['status' => 'error', 'message' => 'Email library missing']);
            return;
        }
        include $vendor;
        try {
            $book = new PHP_Email_Form;
            $book->ajax = true;
            $book->to = 'contact@example.com'; // TODO: update
            $book->from_name = $_POST['name'] ?? '';
            $book->from_email = $_POST['email'] ?? '';
            $book->subject = 'New table booking request from the website';
            $book->add_message($_POST['name'] ?? '', 'Name');
            $book->add_message($_POST['email'] ?? '', 'Email');
            $book->add_message($_POST['phone'] ?? '', 'Phone', 4);
            $book->add_message($_POST['date'] ?? '', 'Date', 4);
            $book->add_message($_POST['time'] ?? '', 'Time', 4);
            $book->add_message($_POST['people'] ?? '', '# of people', 1);
            $book->add_message($_POST['message'] ?? '', 'Message');
            echo $book->send();
        } catch (Throwable $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
