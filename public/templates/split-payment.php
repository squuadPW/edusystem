<div style="margin: 10px">
    <!-- The checkbox -->
    <div style="margin-bottom: 30px; margin-top: -10px">
        <input type="checkbox" id="aes_split_payment" name="aes_split_payment" onchange="showInput(this.checked)"> Use split payment
    </div>

    <!-- The input and button container -->
    <div class="input-container" style="display: none">
        <!-- The input for entering the amount -->
        <div>
            <label for="amount">Amount:</label><br>
            <input type="number" id="amount" name="amount" style="width: 100%"><br><br>
        </div>

        <!-- The button to generate parts -->
         <div style="text-align: center; display: flex; justify-content: space-evenly;">
            <div style="width: 45%; border-top: 1px solid gray;"></div><span style="margin-top: -10px;">Or</span><div style="width: 45%; border-top: 1px solid gray;"></div>
         </div>
        <button id="generate-button" class="button button-primary" style="margin: 10px 0px !important;">Generate payment splits</button>
    </div>
</div>

<script>
		// Function to show or hide the input and button based on the checkbox state
		function showInput(checked) {
			if (checked) {
				document.querySelector('.input-container').style.display = 'block';
			} else {
				document.querySelector('.input-container').style.display = 'none';
			}
		}
	</script>