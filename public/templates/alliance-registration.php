<div class="title">
    Registro de nuevos aliados
    <br> interesados en celebrar convenios con <br> FGU y AES
</div>
<form method="POST" action="<?= the_permalink().'?action=save_alliances'; ?>">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name">Nombres del aliado</label>
            <input class="formdata" type="text" name="name" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname">Apellidos del aliado</label>
            <input class="formdata" type="text" name="last_name" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname">Nombre de representante legal</label>
            <input class="formdata" type="text" name="name_legal" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="phone">Número de contacto</label>
            <input class="formdata number_phone" type="tel" id="number_phone" name="number_phone" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="email">Correo electrónico</label>
            <input class="formdata" type="email" name="email" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="country"><?= __('País de residencia','form-plugin'); ?></label>
            <select name="country">
            <?php foreach($countries as $key => $country){ ?>
                <option value="<?= $key ?>"><?= $country;?></option> 
            <?php } ?>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city">Estado de residencia</label>
            <input class="formdata" type="text" name="state" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city">Ciudad de residencia</label>
            <input class="formdata" type="text" name="city" required>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city">Direccion de residencia</label>
            <input class="formdata" type="text" name="address" required>
        </div>

        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="terms" class="checkboxes">
                <input type="checkbox" id="terms" name="terms" required>
                Acepto los términos y condiciones
            </label>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="politic" class="checkboxes">
                <input type="checkbox"  id="politic" name="politic" required>    
                Acepto la Política de Tratamiento de Datos
            </label>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 bottom">
            <button class="submit">Enviar</button>
        </div>
    </div>
</form>
