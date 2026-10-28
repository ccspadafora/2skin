<!-- Fuente: https://wpamelia.com/documentation/js-hooks-for-appointments/ · descargado 2026-10-27 -->

# How can I use JavaScript hooks in Amelia's appointment booking forms?

Amelia provides JavaScript hooks that allow you to react to user actions and booking flow events on the front end. These hooks can be used to track interactions, validate data, modify behavior, or trigger custom logic during the booking process, without changing Amelia’s core code.

   ![Info](https://wpamelia.com/wp-content/themes/amelia-theme/amelia-blocks/assets/img/docs-sidebar/docs-info-icon.svg)   Note

 Hooks require programming knowledge, and support is limited to general guidance.

These hooks are triggered only on the booking and catalog forms:

- `[ameliastepbooking]`
- `[ameliacatalogbooking]`

## Hook parameters

All JavaScript hooks receive the following parameters:

- **success** – Callback function used to continue the process. Used only for the `beforeBooking` hook.
- **error** – Callback function used to stop the process. Used only for the `beforeBooking` hook.
- **data** – Object containing booking, appointment, or form data related to the current step.

### When is the booking form fully loaded?

The `ViewContent` hook is triggered after the booking form has finished loading.

```

				
					window.ameliaActions = {
  ViewContent: function (success = null, error = null, data) {
    console.log('ViewContent HOOK')
    console.log(data)
    console.log('------------')
  }
}
				
			
```

### When is a service selected or changed?

The `SelectService` hook is triggered when the service is selected or changed in the booking form.

```

				
					window.ameliaActions = {
  SelectService: function (success = null, error = null, data) {
    console.log('SelectService HOOK')
    console.log(data)
    console.log('------------')
  }
}
				
			
```

### When is a category selected or changed?

The `SelectCategory` hook is triggered when the category is selected or changed in the booking form.

```

				
					window.ameliaActions = {
  SelectCategory: function (success = null, error = null, data) {
    console.log('SelectCategory HOOK')
    console.log(data)
    console.log('------------')
  }
}
				
			
```

### When is an employee selected or changed?

The `SelectEmployee` hook is triggered when the employee is selected or changed in the booking form.

```

				
					window.ameliaActions = {
  SelectEmployee: function (success = null, error = null, data) {
    console.log('SelectEmployee HOOK')
    console.log(data)
    console.log('------------')
  }
}
				
			
```

### When is a location selected or changed?

The `SelectLocation` hook is triggered when the location is selected or changed in the booking form.

```

				
					window.ameliaActions = {
  SelectLocation: function (success = null, error = null, data) {
    console.log('SelectLocation HOOK')
    console.log(data)
    console.log('------------')
  }
}
				
			
```

### When is a package selected or changed?

The `SelectPackage` hook is triggered when the package is selected or changed in the booking form.

```

				
					window.ameliaActions = {
  SelectPackage: function (success = null, error = null, data) {
    console.log('SelectPackage HOOK')
    console.log(data)
    console.log('------------')
  }
}
				
			
```

### When is the customer information step initialized?

The `InitInfoStep` hook is triggered when the customer information step is loaded.

```

				
					window.ameliaActions = {
  InitInfoStep: function (success = null, error = null, data) {
    console.log('InitInfoStep HOOK')
    console.log(data)
    console.log('------------')
  }
}
				
			
```

### How can I add custom validation before payment?

The `customValidation` hook is triggered when the customer information step is validated, before the payment step is loaded.

```

				
					window.ameliaActions = {
  customValidation: function (success = null, error = null, data) {
    console.log('customValidation HOOK')
    console.log('------------')
    console.log(data)

    const customValidator = (rule, value, callback) => {
      if (value.includes('SOME_REGEX')) {
        callback(new Error())
      } else {
        callback()
      }
    }

    // example for email field
    data.rules.email.push({
      message: 'CUSTOM MESSAGE',
      validator: customValidator
    })

    // example for a custom field
    var customFieldId = 2
    data.rules['cf' + customFieldId].push({
      message: 'CUSTOM MESSAGE',
      validator: customValidator
    })
  }
}
				
			
```

### When is the payment step initialized?

 The `InitiateCheckout` hook is triggered when the payment step is loaded.

```

				
					window.ameliaActions = {
  InitiateCheckout: function (success = null, error = null, data) {
    console.log('InitiateCheckout HOOK')
    console.log(data)
    console.log('------------')
  }
}
				
			
```

### How can I run logic before a booking is created?

The `beforeBooking` hook is triggered when the Confirm button is clicked, but before the booking is completed. This hook is not available for Mollie and WooCommerce payments.

```

				
					window.ameliaActions = {
  beforeBooking: function (success = null, error = null, data) {
    console.log('Before booking is created HOOK')
    console.log(data)
    console.log('------------')

    // call success() to allow booking completion
    success()
  }
}
				
			
```

### When is an on-site booking completed?

The `Schedule` hook is triggered after an on-site booking is completed, before the congratulations step is loaded.

```

				
					window.ameliaActions = {
  Schedule: function (success = null, error = null, data) {
    console.log('Schedule HOOK')
    console.log(data)
    console.log('------------')
  }
}
				
			
```

### When is an online booking completed?

The `Purchase` hook is triggered after an online booking is completed, before the congratulations step is loaded.

```

				
					window.ameliaActions = {
  Purchase: function (success = null, error = null, data) {
    console.log('Purchase HOOK')
    console.log(data)
    console.log('------------')
  }
}
				
			
```
