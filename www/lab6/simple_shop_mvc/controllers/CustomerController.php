<?php
class CustomerController extends Controller {
    private $customerModel;
    
    public function __construct($database) {
        parent::__construct($database);
        $this->customerModel = new Customer($this->db);
    }
    
    public function index() {
        $message = '';
        
        // Создание покупателя
        if ($_POST && isset($_POST['create'])) {
            $this->customerModel->name = $_POST['name'];
            $this->customerModel->email = $_POST['email'];
            $this->customerModel->phone = $_POST['phone'];
            
            if ($this->customerModel->create()) {
                $message = "Покупатель успешно создан!";
            } else {
                $message = "Ошибка при создании покупателя!";
            }
        }
        //Изменения о покупатели
        if ($_POST && isset($_POST['update'])) {
        $this->customerModel->id = $_POST['id'];
        $this->customerModel->name = $_POST['name'];
        $this->customerModel->email = $_POST['email'];
        $this->customerModel->phone = $_POST['phone'];
            if ($this->customerModel->update()) {
                $message = "Покупатель успешно обновлён!";
                $this->redirect('customers');
            } else {
                $message = "Ошибка при обновлении покупателя!";
            }
        }
        
        // Удаление покупателя
        if (isset($_GET['delete_id'])) {
            $this->customerModel->id = $_GET['delete_id'];
            if ($this->customerModel->delete()) {
                $message = "Покупатель успешно удален!";
            } else {
                $message = "Ошибка при удалении покупателя!";
            }
        }
        
        $customers = $this->customerModel->read();
        
        $this->view('customers/index', [
            'customers' => $customers,
            'message' => $message
        ]);
    }

    public function edit() {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $customer = $this->customerModel->getById($id);
    if (!$customer) {
        $this->redirect('customers');
    }
    $this->view('customers/edit', [
        'customer' => $customer
    ]);
    }

}
?>