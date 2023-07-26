<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-md w-96">
            <h1 class="text-2xl font-bold mb-6">Installer</h1>
            <form id="dbForm" method="post" action="install_process.php" class="space-y-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Database server url:</label>
                    <input type="text" name="dbhost" required
                        class="w-full border rounded-lg py-2 px-3 bg-gray-100 text-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Database name:</label>
                    <input type="text" name="dbname" required
                        class="w-full border rounded-lg py-2 px-3 bg-gray-100 text-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">User of the database:</label>
                    <input type="text" name="dbuser" required
                        class="w-full border rounded-lg py-2 px-3 bg-gray-100 text-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Database password:</label>
                    <input type="password" name="dbpass" required
                        class="w-full border rounded-lg py-2 px-3 bg-gray-100 text-gray-700">
                </div>
                <div class="mb-4">
                    <button type="button" id="testDbButton"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-4 focus:ring-blue-300">
                        Test DB
                    </button>
                </div>
                <div id="testDbResult" class="mb-4"></div>
                <div class="mt-4">
                    <button type="button" id="testFilePermissionsButton"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-4 focus:ring-blue-300">
                        Test File Permissions
                    </button>
                </div>
                <div>
                    <button type="submit" name="install" id="installButton" disabled
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-4 focus:ring-green-300">
                        Install
                    </button>
                </div>
                <div id="testFilePermissionsResult" class="mt-4"></div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            function testFilePermissions() {
                $.ajax({
                    type: "GET",
                    url: "test_file_permissions.php",
                    success: function (response) {
                        $("#testFilePermissionsResult").html(response);
                    },
                    error: function () {
                        $("#testFilePermissionsResult").html("Error: File permissions test failed. Check the required directories and files.");
                    }
                });
            }

            $("#testDbButton").on("click", function () {
                var formData = $("#dbForm").serialize();
                $.ajax({
                    type: "POST",
                    url: "test_connection.php",
                    data: formData,
                    success: function (response) {
                        if (response === "success") {
                            $("#testDbResult").removeClass("text-red-500").addClass("text-green-500").text("Successful connection.");
                            $("#installButton").prop("disabled", false);
                        } else {
                            $("#testDbResult").removeClass("text-green-500").addClass("text-red-500").text("Error: Could not connect to the database.");
                            $("#installButton").prop("disabled", true);
                        }
                    },
                    error: function () {
                        $("#testDbResult").removeClass("text-green-500").addClass("text-red-500").text("Error: Unable to connect to the database.");
                        $("#installButton").prop("disabled", true);
                    }
                });
            });

            $("#testFilePermissionsButton").on("click", function () {
                testFilePermissions();
            });
        });
    </script>
</body>

</html>
