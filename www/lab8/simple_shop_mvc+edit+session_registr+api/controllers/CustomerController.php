<?php
class CustomerController extends Controller {
    private $customerModel;
    
    public function __construct($database, $auth) {
        parent::__construct($database, $auth);
        $this->customerModel = new Customer($this->db);
    }
    
    public function index() {
        $this->auth->requireAdmin();
        
        $message = $this->getMessage();
        
        // Создание покупателя
        if ($_POST && isset($_POST['create'])) {
            $this->customerModel->name = $_POST['name'];
            $this->customerModel->email = $_POST['email'];
            $this->customerModel->phone = $_POST['phone'];
            
            if ($this->customerModel->create()) {
                $this->setMessage("Покупатель успешно создан!", "success");
                $this->redirect('customers');
            } else {
                $message = ["text" => "Ошибка при создании покупателя!", "type" => "danger"];
            }
        }
        
        // Редактирование покупателя
        if ($_POST && isset($_POST['update'])) {
            $this->customerModel->id = $_POST['id'];
            $this->customerModel->name = $_POST['name'];
            $this->customerModel->email = $_POST['email'];
            $this->customerModel->phone = $_POST['phone'];
            
            if ($this->customerModel->update()) {
                $this->setMessage("Покупатель успешно обновлен!", "success");
                $this->redirect('customers');
            } else {
                $message = ["text" => "Ошибка при обновлении покупателя!", "type" => "danger"];
            }
        }
        
        // Удаление покупателя
        if (isset($_GET['delete_id'])) {
            $this->customerModel->id = $_GET['delete_id'];
            if ($this->customerModel->delete()) {
                $this->setMessage("Покупатель успешно удален!", "success");
                $this->redirect('customers');
            } else {
                $message = ["text" => "Ошибка при удалении покупателя!", "type" => "danger"];
            }
        }
        
        $customers = $this->customerModel->read();
        
        $this->view('customers/index', [
            'customers' => $customers,
            'message' => $message
        ]);
    }
    
    public function edit() {
        $this->auth->requireAdmin();
        
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