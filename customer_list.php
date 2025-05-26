<?php

   include('login_session.php');
include "connect.php";

if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($db, $_GET['delete_id']);
    
    $delete_query = "DELETE FROM customer WHERE customer_id = '$delete_id'";
    if (mysqli_query($db, $delete_query)) {
        $_SESSION['update_message'] = "Record deleted successfully.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION['update_message'] = "Failed to delete record.";
    }
}



$type = '';
$sort = $_GET['sort'] ?? '';
$search = $_GET['search'] ?? '';
$searchEscaped = mysqli_real_escape_string($db, $search);

// Build the base SQL and conditions
$sql = "SELECT * FROM customer";
$conditions = [];

if (!empty($search)) {
    $conditions[] = "(lname LIKE '%$searchEscaped%' OR fname LIKE '%$searchEscaped%' OR address LIKE '%$searchEscaped%' OR purok LIKE '%$searchEscaped%' OR landmark LIKE '%$searchEscaped%' OR contact LIKE '%$searchEscaped%' OR type LIKE '%$searchEscaped%')";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Add sorting at the end, not inside WHERE clause
if ($sort === 'az') {
    $sql .= " ORDER BY lname ASC";
} elseif ($sort === 'za') {
    $sql .= " ORDER BY lname DESC";
}

$result = mysqli_query($db, $sql);
if ($result === false) {
    die('Error executing the query: ' . mysqli_error($db));
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $nickname = $_POST['nickname'];
    $address= $_POST['address'];
    $purok = $_POST['purok'];
    $landmark = $_POST['landmark'];
    $contact = $_POST['contact'];
    $type = $_POST['type'] ?? '';
   
     $sql = "INSERT INTO customer (
customer_id, fname, lname,nickname,address,purok,landmark,contact,type)
VALUES (?,?,?,?,?,?,?,?,?)";

$stmt = $db->prepare($sql);
if (!$stmt) {
    die("Prepare failed for records insert: (" . $db->errno . ") " . $db->error);
}

$stmt->bind_param(
    "sssssssss",
    $customer_id,
    $fname,
    $lname,
    $nickname,
    $address,
    $purok,
    $landmark,
    $contact,
    $type
);

if ($stmt->execute()) {
    $_SESSION['update_message'] = "Record added successfully.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
} else {
    $_SESSION['update_message'] = "Failed to add asset.";
}


$stmt->close();
}


$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RTS</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
   
   <link
     rel="stylesheet"
     href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css"
   />
   <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>

</head>
<style>
    .thead ,sr-only{
        background-color: #3498db;
    }

    .auto_num{
        counter-reset: rowNumber;
  counter-increment: rowNumber;
  content: counter(rowNumber) ".";
  padding-right: 0.3em;
  text-align: right;
    }

.content-wrapper{
    overflow-x: auto;
}

@media screen and (max-width: 1024px) {
    .content-wrapper{
       
        overflow-x: auto;
    }
}

@media screen and (max-width: 400px) {
    .content-wrapper{
        
        overflow-x: auto;
    }
}
</style>
<body>

    <?php include("sidebar.php"); ?>

    <div class="">
<div class="bg-white content-wrapper flex items-start justify-center min-h-screen p-2 lg:ml-[250px]">
    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4 mt-10">
 
  
    <div class="relative inline-block">
            
        <button id="dropdownToggle" class="inline-flex items-center text-black bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 font-medium rounded-lg text-sm px-3 py-1.5" type="button">
 
    Sort by Name
    <svg class="w-2.5 h-2.5 ms-2.5" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
</button>

<div id="dropdownMenu" class="absolute z-10 hidden w-28 bg-white divide-y divide-gray-100 rounded-lg shadow-sm">
    <ul class=" space-y-1 text-sm text-gray-700" aria-labelledby="dropdownToggle">
        <li>
            <div class="flex items-center p-2 rounded-sm hover:bg-gray-100">
                <input id="filter-radio-az" type="radio" value="az" name="filter-radio" <?php if ($sort === 'az') echo 'checked'; ?> class="w-4 h-4">
                <label for="filter-radio-az" class="ms-2 text-sm font-medium">A – Z</label>
            </div>
        </li>
        <li>
            <div class="flex items-center p-2 rounded-sm hover:bg-gray-100">
                <input id="filter-radio-za" type="radio" value="za" name="filter-radio" <?php if ($sort === 'za') echo 'checked'; ?> class="w-4 h-4">
                <label for="filter-radio-za" class="ms-2 text-sm font-medium">Z – A</label>
            </div>
        </li>
    </ul>
</div>

</div>
        <label for="table-search" class="sr-only">Search</label>
        <div class="relative justify-end">
            <div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
            </div>
            <form id="searchForm" method="GET" action="">
  <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" id="table-search"
    class="block p-2 ps-10 text-sm text-black border border-gray-300 rounded-lg w-80 focus:ring-blue-500 focus:border-blue-500"
    placeholder="Search" />
</form>


        </div>
        <button id="cAdd" class="modal inline-flex bg-[#3498db] items-center text-white border border-gray-300 focus:outline-none hover:bg-blue-400 font-lg rounded-lg text-md px-3 py-1.5">+</button>


        <div class=" w-full">
        <table class="flex-auto w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mt-2 z-[-1]">
        <thead class="thead text-xs text-white uppercase text-center">
            <tr>
            <th scope="col" class="px-2 sm:px-7 py-2 sm:py-3 md:px-8 md:py-3">
                    No
                </th>
                <th scope="col" class="px-2 sm:px-7 py-2 sm:py-3 md:px-8 md:py-3">
                    Name
                </th>
                <th scope="col" class="px-2 sm:px-7 py-2 sm:py-3 md:px-8 md:py-3">
                    Address
                </th>

                <th scope="col" class="px-2 sm:px-7 py-2 sm:py-3 md:px-8 md:py-3">
                    Contact
                </th>
                <th scope="col" class="px-2 sm:px-7 py-2 sm:py-3 md:px-8 md:py-3">
                    Type
                </th>
                <th scope="col" class="px-2 sm:px-7 py-2 sm:py-3 md:px-8 md:py-3">
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
<?php 
$counter = 1;
if (mysqli_num_rows($result) > 0):
    while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr class="bg-white text-black text-center border-b dark:border-gray-700 border-gray-200 ">
        <th scope="row" class="px-3 py-4 sm:px-2 sm:py-1 font-medium whitespace-nowrap ">
            <?php echo $counter++; ?>
        </th>
        <td>
            <?php echo htmlspecialchars($row['lname']. ' ' .$row['fname']); ?>
        </td>
        <td>
            <?php echo htmlspecialchars($row['address']. ' , ' .$row['purok']. ' , ' .$row['landmark']); ?>
        </td>
        <td>
            <?php echo htmlspecialchars($row['contact']); ?>
        </td>
        <td>
            <?php echo htmlspecialchars($row['type']); ?>
        </td>
        <td>
        <a href="customer_view.php?id=<?php echo $row['customer_id']; ?>" class="font-medium text-yellow-500 hover:underline mr-3">View</a>

            <a href="?delete_id=<?php echo $row['customer_id']; ?>" 
            class="font-medium text-red-600 hover:underline"
            onclick="return confirm('Are you sure you want to delete this customer?');">
            Delete
            </a>
        </td>
        </tr>
    <?php endwhile; 
else: ?>
    <tr>
        <td colspan="6" class="text-center py-4 text-gray-500">No records found.</td>
    </tr>
<?php endif; ?>
</tbody>

    </table>
    </div>
      
<!-- Modal -->
<div id="myModal" class="fixed inset-0 hidden bg-opacity-50 flex items-center justify-center z-50">

  <div class="bg-gray-50 rounded-lg p-6 w-full max-w-md relative border-1 rounded-sm border-black">
    <button id="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
    <h2 class="text-xl font-semibold mb-4">Add Customer</h2>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="text" name="customer_id" class="hidden w-full border p-2 mb-3 rounded">
        <div class="grid grid-cols-3 gap-4">      
            <input type="text" name="lname" placeholder="Last Name" class="w-full border p-2 mb-3 rounded">
            <input type="text" name="fname" placeholder="First Name" class="w-full border p-2 mb-3 rounded">
            <input type="text" name="nickname" placeholder="Nickname" class="w-full border p-2 mb-3 rounded">
        </div>
        <input type="text" name="address" placeholder="Address" class="w-full border p-2 mb-3 rounded">
        <div class="grid grid-cols-2 gap-4">
        <input type="text" name="purok" placeholder="Purok" class="w-full border p-2 mb-3 rounded">
        <input type="text" name="landmark" placeholder="Landmark" class="w-full border p-2 mb-3 rounded">
        </div>
        <div class="grid grid-cols-2 gap-4">
        <input type="text" name="contact" placeholder="Contact" class="w-full border p-2 mb-3 rounded">
<select name="type" id="type" class="w-full border p-2 mb-3 rounded">
  <option value="" disabled hidden <?php echo ($type == '') ? 'selected' : ''; ?>>Type</option>
  <option value="dealer" <?php echo ($type == 'dealer') ? 'selected' : ''; ?>>Dealer</option>
  <option value="residential" <?php echo ($type == 'residential') ? 'selected' : ''; ?>>Residential</option>
</select>


        </div>
      
    
      <div class="flex justify-end space-x-2">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
        <button type="button" id="closeModalBtn" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
      </div>
    </form>
  </div>
</div>


</div>
    </div>
   
</div>
</div>
<!-- Modal Markup Ends Here -->



<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('dropdownToggle');
    const dropdown = document.getElementById('dropdownMenu');
    const radios = document.querySelectorAll('input[name="filter-radio"]');

    toggleBtn.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
    });

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            const selectedValue = radio.value;
            const baseUrl = window.location.href.split('?')[0];
            window.location.href = `${baseUrl}?sort=${selectedValue}`;
        });
    });

    document.addEventListener('click', function (event) {
        if (!dropdown.contains(event.target) && !toggleBtn.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Modal handling
    const modal = document.getElementById("myModal");
    const openBtn = document.getElementById("cAdd");
    const closeBtnX = document.getElementById("closeModal");
    const closeBtnCancel = document.getElementById("closeModalBtn");

    openBtn.addEventListener("click", function (e) {
        e.preventDefault();
        modal.classList.remove("hidden");
    });

    closeBtnX.addEventListener("click", function () {
        modal.classList.add("hidden");
    });

    closeBtnCancel.addEventListener("click", function () {
        modal.classList.add("hidden");
    });

    window.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.classList.add("hidden");
        }
    });
});


</script>

</body>
</html>
