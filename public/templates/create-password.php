<!-- Modal container -->
<div id="modal-contraseña" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="title">
                Create password
            </div>
        </div>
        <div class="modal-body">
            <form method="POST" action="<?= the_permalink(); ?>">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="lastname">Password<span class="required">*</span></label>
                        <input type="password" id="password" name="password" class="form-control">
                    </div>
                    <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                        <label for="lastname">Confirm password<span class="required">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                    </div>
                </div>
                <div style="text-align: center">
                    <button id="save_password" class="btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Styles for the modal -->
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #f9f9f9;
        padding: 20px;
        /* border: 1px solid #091c5c; */
        width: 80%;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }

    .modal-header {
        /* background-color: #091c5c;
        color: #fff; */
        /* padding: 10px; */
        border-bottom: 1px solid #091c5c;
    }

    .modal-header h2 {
        margin-top: 0;
    }

    .form-control {
        width: 100%;
        height: 40px;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .form-control:focus {
        border-color: #091c5c;
        box-shadow: 0 0 10px rgba(9,28,92,0.5);
    }

    .btn {
        background-color: #091c5c;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #061a4c;
    }

    .btn:active {
        background-color: #030a3c;
    }
</style>