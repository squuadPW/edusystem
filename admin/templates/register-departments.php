<style>
    /* Admin Form Styles */

    #my-form {
        max-width: 500px;
        margin: 40px auto;
        padding: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
        color: #333;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
    }

    input[type="text"]:focus,
    textarea:focus {
        border-color: #aaa;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .btn-submit {
        background-color: #4CAF50;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-submit:hover {
        background-color: #3e8e41;
    }
</style>

<form id="my-form" method="post">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
    </div>

    <div class="form-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
    </div>

    <input type="hidden" name="action" value="save_departments">
    <input type="submit" value="Submit" class="btn-submit">
</form>