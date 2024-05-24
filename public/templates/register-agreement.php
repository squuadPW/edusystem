<div class="title">
    Formulario de Registro para Instituciones <br> interesadas en celebrar convenios con <br> FGU y AES
</div>
<form method="post">
    <div class="grid grid-cols-12 gap-4">
        <!-- Section -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <div class="subtitle">Datos del colegio o Institución</div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name">Nombre de Colegio o Institución interesada</label>
            <input class="formdata" type="text" name="name" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="phone">Telefono de Colegio o Institución</label>
            <input class="formdata" type="tel" name="phone" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="email">Correo de Colegio o Institución</label>
            <input class="formdata" type="email" name="email" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="country">País</label>
            <select name="country">
            <?php foreach($countries as $key => $country){ ?>
                <option country="<?php echo $country;?>"><?php echo $country;?></option> 
            <?php } ?>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="state">Estado / Provincia / Dpto</label>
            <input class="formdata" type="text" name="state" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="city">Ciudad</label>
            <input class="formdata" type="text" name="city" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="address">Dirección</label>
            <input class="formdata" type="text" name="address" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="level">Nivel educativo de la institución</label>
            <select name="level">
                <option value="1">Primaria</option>
                <option value="2">Secundaria</option>
            </select>
        </div>

        <!-- Section -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10">
            <div class="subtitle">Contacto</div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="rector_name">Nombre del Rector</label>
            <input class="formdata" type="text" name="rector_name" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="rector_lastname">Apellidos del Rector</label>
            <input class="formdata" type="text" name="rector_lastname" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="rector_phone">Teléfono</label>
            <input class="formdata" type="tel" name="rector_phone" required>
        </div>

        <!-- Section -->
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-10">
            <div class="subtitle">Referencias</div>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="level">Cómo obtuviste información?</label>
            <select name="level">
                <option value="1">Facebook</option>
                <option value="2">Instagram</option>
                <option value="3">Correo electrónico</option>
                <option value="4">Búsqueda por Internet</option>
                <option value="5">Evento presencial</option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="policy" class="checkboxes">
                <input type="checkbox" name="policy" required>
                Acepto la Política de Tratamiento de Datos
            </label>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 bottom">
            <button class="submit">Enviar</button>
        </div>
    </div>
</form>