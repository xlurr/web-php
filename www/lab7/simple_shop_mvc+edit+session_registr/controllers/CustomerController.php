<?php
class CustomerController extends Controller {
    private $customerModel;
    
    public function __construct($database, $auth) {
        parent::__construct($database, $auth);
        $this->customerModel = new Customer($this->db);
    }
    
    public function index() {
        $this->auth->requireAdmin();
        
        $message = null;
        
        // Создание покупателя
        if ($_POST && isset($_POST['create'])) {
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address' => $_POST['address'] ?? ''
            ];
            
            if ($this->customerModel->create($data)) {
                $this->setMessage('Покупатель успешно создан!', 'success');
                $this->redirect('customers');
            } else {
                $message = [
                    'text' => 'Ошибка при создании покупателя',
                    'type' => 'danger'
                ];
            }
        }
        
        // Обновление покупателя
        if ($_POST && isset($_POST['update'])) {
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address' => $_POST['address'] ?? ''
            ];
            
            if ($this->customerModel->update($_POST['id'], $data)) {
                $this->setMessage('Покупатель успешно обновлен!', 'success');
                $this->redirect('customers');
            } else {
                $message = [
                    'text' => 'Ошибка при обновлении покупателя',
                    'type' => 'danger'
                ];
            }
        }
        
        if (isset($_GET['delete']) && !empty($_GET['delete'])) {
            $customerId = intval($_GET['delete']);
            
            if ($this->customerModel->delete($customerId)) {
                $this->setMessage('Покупатель удален!', 'success');
                $this->redirect('customers');
            } else {
                $message = [
                    'text' => 'Ошибка при удалении покупателя',
                    'type' => 'danger'
                ];
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