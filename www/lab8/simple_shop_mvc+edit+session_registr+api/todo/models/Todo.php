<?php
class Todo {
    private array $tasks = [];
    
    public function __construct() {
        $this->tasks = $_SESSION['tasks'] ?? [];
    }
    
    public function add(string $task): void {
        $this->tasks[] = $task;
        $_SESSION['tasks'] = $this->tasks;
    }
    
    public function delete(int $index): void {
        if (isset($this->tasks[$index])) {
            array_splice($this->tasks, $index, 1);
            $_SESSION['tasks'] = $this->tasks;
        }
    }
    
    public function all(): array {
        return $this->tasks;
    }
}
?>
