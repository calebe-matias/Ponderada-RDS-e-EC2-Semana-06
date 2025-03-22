<?php include "../inc/dbinfo.inc"; ?>

<html>
<body>
<h1>Concessionária - Gestão de Veículos</h1>

<?php
$constring = "host=" . DB_SERVER . " dbname=" . DB_DATABASE . " user=" . DB_USERNAME . " password=" . DB_PASSWORD ;
$connection = pg_connect($constring);

if (!$connection){
  echo "Failed to connect to PostgreSQL: " . pg_last_error();
  exit;
}

VerifyCarsTable($connection, DB_DATABASE);

/* Handle Add */
if (isset($_POST['action']) && $_POST['action'] == 'add') {
  $make = htmlentities($_POST['MAKE']);
  $model = htmlentities($_POST['MODEL']);
  $year = (int) $_POST['YEAR'];
  $price = (float) $_POST['PRICE'];
  $status = htmlentities($_POST['STATUS']);

  if ($make && $model && $year && $price) {
    AddCar($connection, $make, $model, $year, $price, $status);
  }
}

/* Handle Delete */
if (isset($_GET['delete'])) {
  $id = (int) $_GET['delete'];
  DeleteCar($connection, $id);
}

/* Handle Edit */
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
  $id = (int) $_POST['ID'];
  $make = htmlentities($_POST['MAKE']);
  $model = htmlentities($_POST['MODEL']);
  $year = (int) $_POST['YEAR'];
  $price = (float) $_POST['PRICE'];
  $status = htmlentities($_POST['STATUS']);
  UpdateCar($connection, $id, $make, $model, $year, $price, $status);
}

/* Handle Edit Form Prefill */
$edit_car = null;
if (isset($_GET['edit'])) {
  $id = (int) $_GET['edit'];
  $result = pg_query($connection, "SELECT * FROM CARS WHERE ID = $id");
  if ($result) $edit_car = pg_fetch_assoc($result);
}
?>

<h2><?php echo $edit_car ? "Editar Veículo" : "Adicionar Novo Veículo"; ?></h2>
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <input type="hidden" name="action" value="<?php echo $edit_car ? 'edit' : 'add'; ?>" />
  <?php if ($edit_car) echo '<input type="hidden" name="ID" value="' . $edit_car['id'] . '" />'; ?>
  <table border="0">
    <tr><td>Marca:</td><td><input type="text" name="MAKE" value="<?php echo $edit_car['make'] ?? ''; ?>" /></td></tr>
    <tr><td>Modelo:</td><td><input type="text" name="MODEL" value="<?php echo $edit_car['model'] ?? ''; ?>" /></td></tr>
    <tr><td>Ano:</td><td><input type="number" name="YEAR" value="<?php echo $edit_car['year'] ?? ''; ?>" /></td></tr>
    <tr><td>Preço:</td><td><input type="number" step="0.01" name="PRICE" value="<?php echo $edit_car['price'] ?? ''; ?>" /></td></tr>
    <tr><td>Status:</td><td><input type="text" name="STATUS" value="<?php echo $edit_car['status'] ?? 'disponível'; ?>" /></td></tr>
    <tr><td colspan="2"><input type="submit" value="<?php echo $edit_car ? 'Salvar Alterações' : 'Adicionar Carro'; ?>" /></td></tr>
  </table>
</form>

<h2>Lista de Veículos</h2>
<table border="1" cellpadding="4" cellspacing="0">
  <tr>
    <th>ID</th><th>Marca</th><th>Modelo</th><th>Ano</th><th>Preço</th><th>Status</th><th>Ações</th>
  </tr>

<?php
$result = pg_query($connection, "SELECT * FROM CARS ORDER BY ID");

while ($row = pg_fetch_assoc($result)) {
  echo "<tr>";
  echo "<td>{$row['id']}</td>";
  echo "<td>{$row['make']}</td>";
  echo "<td>{$row['model']}</td>";
  echo "<td>{$row['year']}</td>";
  echo "<td>R$ " . number_format($row['price'], 2, ',', '.') . "</td>";
  echo "<td>{$row['status']}</td>";
  echo "<td>
          <a href='?edit={$row['id']}'>Editar</a> |
          <a href='?delete={$row['id']}' onclick='return confirm(\"Tem certeza?\")'>Excluir</a>
        </td>";
  echo "</tr>";
}
pg_free_result($result);
pg_close($connection);
?>
</table>
</body>
</html>

<?php
function AddCar($connection, $make, $model, $year, $price, $status) {
  $query = "INSERT INTO CARS (MAKE, MODEL, YEAR, PRICE, STATUS) 
            VALUES ('" . pg_escape_string($make) . "', '" . pg_escape_string($model) . "', $year, $price, '" . pg_escape_string($status) . "')";
  if (!pg_query($connection, $query)) echo "<p>Erro ao adicionar veículo.</p>";
}

function UpdateCar($connection, $id, $make, $model, $year, $price, $status) {
  $query = "UPDATE CARS SET 
            MAKE = '" . pg_escape_string($make) . "',
            MODEL = '" . pg_escape_string($model) . "',
            YEAR = $year,
            PRICE = $price,
            STATUS = '" . pg_escape_string($status) . "'
            WHERE ID = $id";
  if (!pg_query($connection, $query)) echo "<p>Erro ao atualizar veículo.</p>";
}

function DeleteCar($connection, $id) {
  $query = "DELETE FROM CARS WHERE ID = $id";
  if (!pg_query($connection, $query)) echo "<p>Erro ao excluir veículo.</p>";
}

function VerifyCarsTable($connection, $dbName) {
  if (!TableExists("CARS", $connection, $dbName)) {
    $query = "CREATE TABLE CARS (
      ID serial PRIMARY KEY,
      MAKE VARCHAR(50),
      MODEL VARCHAR(50),
      YEAR INT,
      PRICE NUMERIC(10,2),
      STATUS VARCHAR(30)
    )";
    if (!pg_query($connection, $query)) echo "<p>Erro ao criar tabela CARS.</p>";
  }
}

function TableExists($tableName, $connection, $dbName) {
  $t = strtolower(pg_escape_string($tableName));
  $query = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t';";
  $check = pg_query($connection, $query);
  return (pg_num_rows($check) > 0);
}
?>
