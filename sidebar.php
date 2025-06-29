<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="/dist/tailwind.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css"
    />
  </head>
  <style>
    .content-wrapper {
    position: relative;
    z-index: 100;
}


.color{
  background: #3498db;

}

@media screen and (max-width: 1024px) {
  .sidebar {
    width: 100%;
    height: auto;
    position: relative;
  }
  .sidebar a {float: left;}
  div.content {margin-left: 0;}
}

@media screen and (max-width: 400px) {
  .sidebar a {
    text-align: center;
    float: none;
  }
}
  </style>
  <body class="flex-auto content-wrapper">
    <span
      class="absolute text-white text-4xl top-5 left-4 cursor-pointer"
      onclick="openSidebar()"
    >
      <i class="bi bi-filter-left px-2 bg-gray-900 rounded-md"></i>
    </span>
    <div
      class="color sidebar fixed top-0 bottom-0 lg:left-0 p-2 w-[250px]  overflow-y-auto text-center bg-gray-900"
    >
      <div class="text-gray-100 text-xl">
        <div class="p-2.5 mt-1 flex justify-center">
         
   <img src="rts.png" alt="" class="h-20 w-40">
        </div>

      </div>

      <a href="dashboard.php"><div
        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-gray-100 hover:text-black text-white"
      >
        <i class="bi bi-house-door-fill"></i>
        
        <span class="text-[15px] ml-4  font-bold">Dashboard</span>
      </div></a>
      <a href="customer_list.php"><div
        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-gray-100 hover:text-black text-white"
      >
      <i class="bi bi-person"></i>
      <span class="text-[15px] ml-4  font-bold">Customer List</span>
      </div>
      </a>
      
</div>

    <script type="text/javascript">
      function dropdown() {
        document.querySelector("#submenu").classList.toggle("hidden");
        document.querySelector("#arrow").classList.toggle("rotate-0");
      }
      dropdown();

      function openSidebar() {
        document.querySelector(".sidebar").classList.toggle("hidden");
      }
    </script>
  </body>
</html>