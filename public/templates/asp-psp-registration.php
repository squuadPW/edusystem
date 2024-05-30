<div class="title">
    REGISTRO DE ESTUDIANTES <br> ASPIRANTES AES-PSP
</div>
<form method="POST" action="<?= the_permalink().'?action=save_student'; ?>">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name">Nombres del estudiante</label>
            <input class="formdata" type="text" name="name_student" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="lastname">Apellidos del estudiante</label>
            <input class="formdata" type="text" name="lastname_student" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="phone">Número de contacto del estudiante</label>
            <input class="formdata number_phone" type="tel" id="number_phone" name="number_phone" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="email">Correo electrónico del estudiante</label>
            <input class="formdata" type="email" name="email_student" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="agent_name">Nombres del Padre, Madre o Acudiente</label>
            <input class="formdata" type="text" name="agent_name" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="agent_name">Apellidos del Padre, Madre o Acudiente</label>
            <input class="formdata" type="text" name="agent_last_name" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="phone">Numero de contacto del del Padre, Madre o Acudiente</label>
            <input class="formdata number_phone" type="tel" id="number_partner" name="number_partner" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="email">Correo electrónico del del Padre, Madre o Acudiente</label>
            <input class="formdata" type="email" name="email_partner" required>
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
            <label for="city">Ciudad de residencia</label>
            <input class="formdata" type="text" name="city" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="birth_date">Fecha de nacimiento</label>
            <input class="formdata" type="text" id="birth_date" name="birth_date" required>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="grade">Grado del estudiante</label>
            <select name="grade">
                <option value="1">9no (antepenúltimo)</option>
                <option value="2">10mo (penúltimo)</option>
                <option value="3">11vo (último)</option>
                <option value="4">Bachiller (graduado)</option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="program">Programa de su interés</label>
            <select name="program">
                <option value="aes">AES (Dual Diploma)</option>
                <option value="psp">PSP (Carrera Universitaria)</option>
                <option value="aes_psp">AMBOS</option>
            </select>
        </div>
        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
            <label for="name_institute">Nombre del Colegio o Institución con Convenio</label>
            <select name="name_institute" id="name_institute">
                <option value=""><?= __('Select a institute','aes'); ?></option>
                <?php foreach($institutes as $institute): ?>
                    <option value="<?= $institute->name ?>"><?= $institute->name; ?></option>
                <?php endforeach; ?>
            </select>
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
    
