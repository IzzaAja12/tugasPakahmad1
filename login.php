<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Muhamad Nauval Azhar">
    <meta name="description" content="This is a login page template based on Tailwind CSS">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <section class="h-screen flex items-center justify-center">
        <div class="container mx-auto px-4">
            <div class="flex justify-center">
                <div class="w-full max-w-md">
                    <div class="text-center mb-8">
                        <img src="https://getbootstrap.com/docs/5.0/assets/brand/bootstrap-logo.svg" alt="logo" class="w-24 mx-auto">
                    </div>
                    <div class="bg-white shadow-lg rounded-lg p-8">
                        <h1 class="text-2xl font-bold text-center mb-6">Login</h1>
                        <form method="POST" action="login_proses.php" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                <input id="username" type="text" name="username" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">
                                    Username is required
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input id="password" type="password" name="password" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">
                                    Password is required
                                </div>
                            </div>
                            <?php
                                if(isset($_GET['error'])) :
                            ?>
                                 <div class="invalid-feedback text-red-500 font-medium text-sm mt-1 mb-2">
                                    <?= $_GET['error'] ?>
                                 </div>
                            <?php
                            endif;
                            ?>
                           
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="js/login.js"></script>
</body>

</html>