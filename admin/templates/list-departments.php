<?php
function list_departments()
{
    ob_start();
?>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .title {
            font-weight: 600 !important;
            color: black;
            border-bottom: 1px solid #f2f2f2;
            margin-top: 20px !important;
            font: 24px / 33px "Helvetica Neue", Helvetica, Arial, sans-serif;
        }

        .table {
            padding: 18px;
            width: 100% !important;
        }

        table {
            border-collapse: collapse;
            width: 100% !important;
        }

        th,
        td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .delete {
            background-color: red;
            color: white;
            padding: 5px;
            border-radius: 4px;
        }

        .edit {
            background-color: #00AEEF;
            color: white;
            padding: 5px;
            border-radius: 4px;
        }
    </style>

    <body>
        <div class="title">
            LISTADO DE DEPARTAMENTOS
        </div>
        <div class="table">
            <table class="table-auto">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>The Sliding Mr. Bones (Next Stop, Pottersville)</td>
                        <td>Malcolm Lockyer</td>
                        <td>
                            <button class="delete">
                                <i class="dashicons dashicons-trash"></i>
                            </button>

                            <button class="edit">
                                <i class="dashicons dashicons-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>Witchy Woman</td>
                        <td>The Eagles</td>
                        <td>
                            <button class="delete">
                                <i class="dashicons dashicons-trash"></i>
                            </button>

                            <button class="edit">
                                <i class="dashicons dashicons-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>Shining Star</td>
                        <td>Earth, Wind, and Fire</td>
                        <td>
                            <button class="delete">
                                <i class="dashicons dashicons-trash"></i>
                            </button>

                            <button class="edit">
                                <i class="dashicons dashicons-edit"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
<?php
    return ob_get_clean();
}
add_shortcode('list_departments', 'list_departments');
