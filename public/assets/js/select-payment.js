document.addEventListener("DOMContentLoaded", function () {
  document.querySelector("select[name=country]").value =
    getCookie("billing_country");
    changeSelectCountry(getCookie("billing_country"));
  document.querySelector("input[name=billing_address_1]").value =
    getCookie("billing_address_1");
  document.querySelector("input[name=city]").value = getCookie("billing_city");
  document.querySelector("input[name=billing_postcode]").value =
    getCookie("billing_postcode");

  let select_country_step_two = document.getElementById(
    "country-select-step-two"
  );
  if (select_country_step_two) {
    select_country_step_two.addEventListener("change", function (e) {
      changeSelectCountry(e.target.value);
    });
  }

  let select_payment_methods = document.querySelectorAll(
    ".card-select-payment"
  );
  if (select_payment_methods.length > 0) {
    // Verifica si hay elementos seleccionados
    select_payment_methods.forEach((payment) => {
      payment.addEventListener("click", function (e) {
        // Remover la clase .card-selected-payment de todos los elementos
        select_payment_methods.forEach((p) =>
          p.classList.remove("card-selected-payment")
        );

        // Agregar la clase .card-selected-payment al elemento que fue clickeado
        e.currentTarget.classList.add("card-selected-payment");

        // Obtener el data-id del elemento clickeado
        let paymentId = e.currentTarget.dataset.id;

        // Asignar el valor al input correspondiente
        document.querySelector("input[name=payment_method_selected]").value =
          paymentId;
      });
    });
  }

  let buttonsave_secondary = document.getElementById("buttonsave_secondary");
  if (buttonsave_secondary) {
    // Verifica si hay elementos seleccionados
    buttonsave_secondary.addEventListener("click", function (e) {
      const hadPaymentMethodSelected = document.querySelectorAll(
        ".card-selected-payment"
      );
      if (hadPaymentMethodSelected.length > 0) {
        document.getElementById("buttonsave").click();
      } else {
        alert("Please select a payment method.");
      }
    });
  }
});

// Función para obtener el valor de una cookie por su nombre
function getCookie(name) {
  let cookieArr = document.cookie.split(";");
  for (let i = 0; i < cookieArr.length; i++) {
    let cookiePair = cookieArr[i].split("=");
    // Eliminamos espacios en blanco y verificamos si el nombre de la cookie coincide
    if (name === cookiePair[0].trim()) {
      return decodeURIComponent(cookiePair[1]);
    }
  }
  // Retornamos null si no se encuentra la cookie
  return null;
}

function changeSelectCountry(value) {
  let select_state_step_two = document.getElementById("state-select-step-two"); 
  select_state_step_two.disabled = true;
  let action = "action=get_states_country";
  const XHR = new XMLHttpRequest();
  XHR.open("POST", `${ajax_object.ajax_url}?${action}`, true);
  XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  XHR.responseType = "json";
  let params = `${action}&option=${value}`;
  XHR.send(params);

  XHR.onload = function () {
    if (XHR.status === 200) {
      // Limpiar el select de estados antes de llenarlo
      select_state_step_two.innerHTML = "";

      // Crear una opción por defecto
      let defaultOption = document.createElement("option");
      defaultOption.value = "";
      defaultOption.selected = true;
      defaultOption.textContent = "Select an option"; // Cambia el texto según sea necesario
      select_state_step_two.appendChild(defaultOption);

      // Recorrer los estados y crear las opciones
      select_state_step_two.required = false;
      if (XHR.response && XHR.response.states) {
        for (let key in XHR.response.states) {
          if (XHR.response.states.hasOwnProperty(key)) {
            let option = document.createElement("option");
            option.value = key; // Código del estado
            option.textContent = XHR.response.states[key]; // Nombre del estado
            select_state_step_two.appendChild(option);
          }
        }
        select_state_step_two.disabled = false;
        select_state_step_two.required = true;
        if (getCookie("billing_state")) {
            document.querySelector("select[name=billing_state]").value = getCookie("billing_state");
        }
      }
    }
  };
}
