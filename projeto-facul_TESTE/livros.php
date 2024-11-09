<!-- livros.php -->
<?php
require_once 'config.php';

// Processar a exclusão
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    
    // Verificar se existem reservas para este livro
    $check_reservas = "SELECT COUNT(*) as count FROM reservas WHERE livro_id = ?";
    $stmt = $conn->prepare($check_reservas);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $message = [
            'type' => 'danger',
            'text' => 'Não é possível excluir este livro pois existem reservas associadas a ele.'
        ];
    } else {
        $sql = "DELETE FROM livros WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = [
                'type' => 'success',
                'text' => 'Livro excluído com sucesso!'
            ];
        } else {
            $message = [
                'type' => 'danger',
                'text' => 'Erro ao excluir o livro: ' . $stmt->error
            ];
        }
    }
}

// Buscar todos os livros
$sql = "SELECT id, titulo, autor, editora, ano FROM livros";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Livros</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Bem-vindo ao Sistema de Reserva de Livros</h1>
        <div class="text-center mb-4">
            <a href="novo_usuario.php" class="btn btn-primary mr-2">Novo Usuário</a>
            <a href="novo_livro.php" class="btn btn-primary mr-2">Novo Livro</a>
            <a href="historico_emprestimos.php" class="btn btn-primary mr-2">Histórico de Empréstimos</a>
            <a href="login.php" class="btn btn-primary mr-2">Login</a>
            <a href="emprestimos.php" class="btn btn-primary mr-2">Empréstimos</a>
            <a href="livros.php" class="btn btn-primary mr-2">Gerenciar Livros</a>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $message['text']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <h2 class="text-center mb-4">Gerenciar Livros</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Editora</th>
                        <th>Ano</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["titulo"] . "</td>";
                            echo "<td>" . $row["autor"] . "</td>";
                            echo "<td>" . $row["editora"] . "</td>";
                            echo "<td>" . $row["ano"] . "</td>";
                            echo "<td>
                                    <form method='POST' class='d-inline' onsubmit='return confirm(\"Tem certeza que deseja excluir este livro?\");'>
                                        <input type='hidden' name='delete_id' value='" . $row["id"] . "'>
                                        <button type='submit' class='btn btn-danger btn-sm'>
                                            Excluir
                                        </button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>Nenhum livro cadastrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>