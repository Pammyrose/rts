<?php
include 'connect.php'; // Make sure this file sets $conn

$customer_id = $lname = $fname = $nickname = $address = $purok = $landmark = $contact = $type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission for insert or update
    $customer_id = $_POST['customer_id'];
    $lname = $_POST['lname'];
    $fname = $_POST['fname'];
    $nickname = $_POST['nickname'];
    $address = $_POST['address'];
    $purok = $_POST['purok'];
    $landmark = $_POST['landmark'];
    $contact = $_POST['contact'];
    $type = $_POST['type'];

    if (!empty($customer_id)) {
        // Update existing customer
        $stmt = $db->prepare("UPDATE customer SET lname=?, fname=?, nickname=?, address=?, purok=?, landmark=?, contact=?, type=? WHERE customer_id=?");
        $stmt->bind_param("ssssssssi", $lname, $fname, $nickname, $address, $purok, $landmark, $contact, $type, $customer_id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Insert new customer
        $stmt = $db->prepare("INSERT INTO customers (lname, fname, nickname, address, purok, landmark, contact, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $lname, $fname, $nickname, $address, $purok, $landmark, $contact, $type);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: customer_list.php"); // Redirect after save
    exit();
}

// If editing, load customer data
if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];
    $result = $db->query("SELECT * FROM customer WHERE customer_id = $edit_id");
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $customer_id = $row['customer_id'];
        $lname = $row['lname'];
        $fname = $row['fname'];
        $nickname = $row['nickname'];
        $address = $row['address'];
        $purok = $row['purok'];
        $landmark = $row['landmark'];
        $contact = $row['contact'];
        $type = $row['type'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RTS</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css"/>
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
</head>
<body class="bg-gray-100">
<?php include("sidebar.php"); ?>

  <!-- Backdrop Overlay -->
  <div class="fixed inset-0 bg-opacity-40 "></div>

  <!-- Modal container -->
  <div id="myModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle"
    class="fixed top-20 left-1/2 transform -translate-x-1/2 rounded-lg p-6 w-full max-w-md shadow-xl border bg-white">

    <button id="closeModal" onclick="window.location.href='customer_list.php'"
      class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold" aria-label="Close modal">&times;</button>

    <h2 id="modalTitle" class="text-xl font-semibold mb-6"><?php echo $customer_id ? 'Update Customer' : 'Add Customer'; ?></h2>

    <form method="POST" action="">
      <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($customer_id); ?>">

      <div class="grid grid-cols-3 gap-4 mb-4">
        <input type="text" name="lname" placeholder="Last Name" class="border p-2 rounded" value="<?php echo htmlspecialchars($lname); ?>" required>
        <input type="text" name="fname" placeholder="First Name" class="border p-2 rounded" value="<?php echo htmlspecialchars($fname); ?>" required>
        <input type="text" name="nickname" placeholder="Nickname" class="border p-2 rounded" value="<?php echo htmlspecialchars($nickname); ?>">
      </div>

      <input type="text" name="address" placeholder="Address" class="w-full border p-2 mb-4 rounded" value="<?php echo htmlspecialchars($address); ?>" required>

      <div class="grid grid-cols-2 gap-4 mb-4">
        <input type="text" name="purok" placeholder="Purok" class="border p-2 rounded" value="<?php echo htmlspecialchars($purok); ?>">
        <input type="text" name="landmark" placeholder="Landmark" class="border p-2 rounded" value="<?php echo htmlspecialchars($landmark); ?>">
      </div>

      <div class="grid grid-cols-2 gap-4 mb-6">
        <input type="text" name="contact" placeholder="Contact" class="border p-2 rounded" value="<?php echo htmlspecialchars($contact); ?>" required>
        <select name="type" class="border p-2 rounded" required>
          <option value="" disabled <?php echo ($type == '') ? 'selected' : ''; ?>>Select Type</option>
          <option value="dealer" <?php echo ($type == 'dealer') ? 'selected' : ''; ?>>Dealer</option>
          <option value="residential" <?php echo ($type == 'residential') ? 'selected' : ''; ?>>Residential</option>
        </select>
      </div>

      <div class="flex justify-end space-x-3">
        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
          <?php echo $customer_id ? 'Update' : 'Save'; ?>
        </button>
        <button type="button" onclick="window.location.href='customer_list.php'"
          class="bg-gray-300 text-gray-800 px-5 py-2 rounded hover:bg-gray-400 transition">Cancel</button>
      </div>
    </form>
  </div>
  <script>
  // Open modal function
  function openModal(customerData = null) {
    document.getElementById('modalBackdrop').classList.remove('hidden');
    document.getElementById('modalForm').classList.remove('hidden');

    if (customerData) {
      // Fill form with existing customer data for editing
      document.getElementById('modalTitle').innerText = 'Update Customer';
      document.getElementById('customer_id').value = customerData.customer_id;
      // Set other form fields similarly (lname, fname, etc)
      // Example:
      document.querySelector('input[name="lname"]').value = customerData.lname;
      document.querySelector('input[name="fname"]').value = customerData.fname;
      // ... fill other inputs
    } else {
      // Clear form for new customer
      document.getElementById('modalTitle').innerText = 'Add Customer';
      document.getElementById('customerForm').reset();
      document.getElementById('customer_id').value = '';
    }
  }

  // Close modal function
  function closeModal() {
    document.getElementById('modalBackdrop').classList.add('hidden');
    document.getElementById('modalForm').classList.add('hidden');
  }

  document.getElementById('closeModal').addEventListener('click', closeModal);
  document.getElementById('cancelModal').addEventListener('click', closeModal);

  // Example: open modal on button click (adjust your button IDs)
  document.getElementById('addCustomerBtn').addEventListener('click', () => openModal());

  // For editing, call openModal with customer data
  // e.g. openModal({customer_id: 1, lname: 'Smith', fname: 'John', ...});
</script>

</body>
</html>