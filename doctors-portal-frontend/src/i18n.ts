// src/i18n.ts
import { createI18n } from 'vue-i18n'

const messages = {
  en: {
    header: {
      doctorLogin: 'Doctor Login'
    },
    dashboard: {
      greeting: "Hello, {name}",
      subtext: "Track your activity and progress below.",
      cards: {
        doctors: {
          title: "Active Doctors",
          linkText: "See doctors"
        },
        prescriptions: {
          title: "Prescriptions Today",
          linkText: "View all"
        },
        completed: {
          title: "Completed Today",
          linkText: "Check completed"
        },
        month: {
          title: "This Month",
          linkText: "Monthly stats"
        }
      }
    },
    settings: {
      title: 'Settings',
      caretend: 'CareTend API',
      clientId: 'Client ID',
      secretKey: 'Secret Key',
      placeholderClientId: 'Enter Client ID',
      placeholderSecretKey: 'Enter Secret Key',
      save: 'Save',
      update: 'Update',
      delete: 'Delete',
      requiredClientId: 'Client ID is required',
      requiredSecret: 'Secret is required',
      successSave: 'Credential saved',
      successUpdate: 'Credential updated',
      successDelete: 'Credential deleted',
      errorSave: 'Failed to save credential',
      errorDelete: 'Failed to delete credential',
      languageTitle: 'Select language'
    },
    chart: {
      orders: 'Orders',
      earnings: 'Earnings',
      refunds: 'Refunds',
      months: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    },
    pieChart: {
      title: 'Completed prescription by %',
      labels: ['Lisinopril', 'Levothyroxine', 'Atorvastatin', 'Metformin']
    },
    footer: {
      contact: 'Contact Us',
      terms: 'Terms of Service',
      copyright: 'DeliverIt Pharmacy'
    },
    layout: {
      menu: {
        dashboard: 'Dashboard',
        admins: 'Admins',
        doctors: 'Doctors',
        profile: 'Profile',
        settings: 'Settings',
        referral: 'Referral Forms',
        logout: 'Logout'
      },
      partners: 'Partnered With',
      logoutSuccess: 'Logged out successfully',
      logoutError: 'Failed to logout'
    },
    adminUsers: {
      title: 'Manage Admins',
      searchPlaceholder: 'Search users...',
      addUser: 'Add User',
      editUser: 'Edit User',
      addNewAdmin: 'Add New Admin',
      name: 'Name',
      email: 'Email',
      password: 'Password',
      confirmPassword: 'Confirm Password',
      save: 'Save',
      cancel: 'Cancel',
      edit: 'Edit',
      delete: 'Delete',
      deleteTitle: 'Delete User',
      deleteConfirm: 'Are you sure you want to delete this user?',
      deleteSuccess: 'User deleted successfully!',
      addSuccess: 'User added successfully!',
      updateSuccess: 'User updated successfully!',
      errorSave: 'Failed to save user',
      errorDelete: 'Failed to delete user',
      errorLoad: 'Failed to load users',
      validation: {
        name: 'Name is required',
        email: 'Email is required',
        invalidEmail: 'Invalid email',
        passwordLength: 'Password must be at least 6 characters',
        passwordMatch: 'Passwords do not match'
      }
    },
     doctors: {
      title: 'Manage Doctors',
      searchPlaceholder: 'Search doctors...',
      add: 'Add Doctor',
      edit: 'Edit Doctor',
      addNew: 'Add New Doctor',
      name: 'Name',
      email: 'Email',
      sheetId: 'Sheet Identifier',
      password: 'Password',
      confirmPassword: 'Confirm Password',
      save: 'Save',
      cancel: 'Cancel',
      prescriptions: 'Prescriptions',
      delete: 'Delete',
      deleteTitle: 'Delete User',
      deleteConfirm: 'Are you sure you want to delete this user? This action cannot be undone.',
      deleteSuccess: 'User deleted successfully!',
      addSuccess: 'User added successfully!',
      updateSuccess: 'User updated successfully!',
      errorSave: 'Failed to save user',
      errorDelete: 'Failed to delete user',
      errorLoad: 'Failed to load users',
      validation: {
        name: 'Name is required',
        email: 'Email is required',
        invalidEmail: 'Invalid email',
        sheet: 'The sheet identifier is required',
        passwordLength: 'Password must be at least 6 characters',
        passwordMatch: 'Passwords do not match'
      }
    },
    profile: {
      title: 'Your Profile',
      name: 'Name',
      email: 'Email',
      newPassword: 'New password',
      confirmPassword: 'Confirm password',
      placeholder: {
        name: 'Your name',
        email: 'Your email',
        newPassword: 'New password',
        confirmPassword: 'Confirm password'
      },
      update: 'Update Profile',
      success: 'Profile updated successfully!',
      error: 'Failed to update profile. Please check the form.',
      fetchError: 'Failed to fetch user data',
      validation: {
        name: 'Name is required',
        email: 'Email is required',
        invalidEmail: 'Invalid email format',
        passwordMin: 'Password must be at least 6 characters',
        passwordMatch: 'Passwords do not match'
      }
    },
    dashboardPrescription: {
      profileTitle: 'Your Profile',
      currentlyViewing: '',
      viewing: 'You are currently viewing',
      prescriptionRefreshNote: 'These prescriptions are linked to this doctor and refresh every 30 minutes from our system.',
      needHelp: 'Need help? Contact us:',
      phone: 'Phone',
      fax: 'Fax',
      goBack: 'Go Back',
      addPrescription: 'Add Prescription',
      editPrescription: 'Edit Prescription',
      createPrescription: 'Create Prescription',
      addShipment: "Shipment created!",
      editShipment: "Shipment updated!",
      errorShipment: "Could not save shipment.",
      errorUploadShipment: "Unable to load shipment updates.",
      patientName: 'Patient Name',
      dob: 'Date of Birth',
      dobPlaceHolder: 'Select date',
      prescriptionName: 'Prescription Name',
      source: 'Source',
      insurance: 'Insurance',
      city: 'City',
      status: 'Status',
      arrivedToOffice: 'Arrived to Office Date',
      cancel: 'Cancel',
      save: 'Save',
      searchPlaceholder: 'Search prescriptions or patients...',
      edit: 'Edit',
      delivered: 'Delivery Confirmation',
      collecting: 'Collecting Co-pay',
      copay: 'Co-pay Assistant or Foundation Assistant',
      insuranceVerification: 'Insurance Verification & Authorization',
      intake: 'Intake Processing',
      dataEntry: 'Data Entry',
      confirmed: 'Confirmed Prescription Received',
      production: 'Production',
      unknown: 'Unknown',
      table: {
          id: 'ID',
          patient: 'Paciente',
          prescription: 'Receta',
          dob: 'Fecha de Nacimiento',
          insurance: 'Seguro',
          city: 'Ciudad',
          source: 'Fuente',
          arrived: 'Fecha de Llegada',
          status: 'Estado',
          actions: 'Acciones',
          edit: 'Editar'
        }
    },
    emailVerified: {
      title: 'Email Verified',
      message: 'Your email has been successfully verified. You can now log in.',
      button: 'Go to Login'
    },
    forgot: {
      title: 'Reset Password',
      emailLabel: 'Email',
      emailPlaceholder: 'Enter your registered email',
      sendLink: 'Send Reset Link',
      backToLogin: 'Back to Login'
    },
    home: {
      welcome: "Welcome to DeliverIt's Doctor Portal",
      subtitle: "Streamline prescription management and referral tracking with real-time integration.",
      login: "Doctor Login",
      toolsTitle: "Powerful Tools for Smarter Care",
      toolsDescription: "Monitor prescriptions, referrals, and patient statuses in real time — all from one intuitive, doctor-focused platform.",
      features: {
        statusTitle: "Prescription Status",
        statusDesc: "View and manage prescriptions marked as Active, Inactive, or Processing — updated in real-time via CaredAPI.",
        referralTitle: "Referral Workflow",
        referralDesc: "Stay informed on each referral’s journey — from Benefits Verification to Insurance Approval or Denial.",
        patientTitle: "Patient Status",
        patientDesc: "Track patient activation manually or programmatically. Ensure visibility into each patient’s current state."
      },
      faq: {
        title: "Frequently Asked Questions",
        description: "If you cannot find an answer to your question in our FAQ, you can always contact us or email us. We will answer you shortly!",
        email: "Email Us",
        general: "General Questions",
        q1: "How can I track the status of prescriptions in real time?",
        a1: "Our platform syncs directly with CaredAPI to give you real-time updates on every prescription.",
        q2: "What stages are included in the referral workflow?",
        a2: "The referral workflow includes Benefits Verification, Insurance Approval, Denial, and other key steps.",
        q3: "Can I see whether a patient has been activated?",
        a3: "Yes. You can monitor patient activation manually or programmatically through the portal.",
        q4: "How does the system integrate with CaredAPI for prescription updates?",
        a4: "Our integration with CaredAPI pulls prescription updates in real time.",
        q5: "Is it possible to monitor multiple patient statuses from one dashboard?",
        a5: "Absolutely. Our doctor-focused dashboard gives you an overview of all patient statuses at once.",
        q6: "What types of prescription states are supported?",
        a6: "We currently support Active, Inactive, and Processing states.",
        q7: "How frequently is patient data updated in the portal?",
        a7: "Patient data is updated in real time as events occur.",
        privacy: "Privacy & Security",
        q8: "Is the platform HIPAA compliant?",
        a8: "Yes. Our platform is fully HIPAA compliant.",
        q9: "How is patient data protected?",
        a9: "Patient data is encrypted both in transit and at rest.",
        q10: "Who can access medical information on the platform?",
        a10: "Only verified and authorized users such as licensed physicians can access patient data.",
        q11: "Do you store or process any data outside the U.S.?",
        a11: "No. All data is stored and processed within secure U.S.-based data centers."
      },
      contact: {
        title: "Contact Us",
        email: "Email: doctors@deliverit.health",
        phone: "Phone: (555) 123-4567"
      }
    },
    login: {
      title: "Login",
      email: "Email",
      emailPlaceholder: "Enter your email",
      password: "Password",
      passwordPlaceholder: "Enter your password",
      rememberMe: "Remember me",
      forgotPassword: "Forgot password?",
      signIn: "Sign in",
      loginMsg: {
        emailMsg : "Please input your email",
        emailMsgValid : "Please enter a valid email address",
        emailPwd : "Please input your password",
        emailPwdCh : "Password must be at least 6 characters",

      },
      errorMessage: "Login failed, please check your credentials"
    },
    userDash: {
      greeting: 'Hello',
      subtext: 'Here you can see the status of your prescriptions. Updates are pulled from our internal system every 30 minutes to ensure accuracy.',
      needHelp: 'Need help? Contact us:',
      phone: 'Phone',
      fax: 'Fax',
      searchPlaceholder: 'Search prescriptions or patients...',
      table: {
        patient: 'Patient',
        prescription: 'Prescription',
        dob: 'DOB',
        insurance: 'Insurance',
        city: 'City',
        source: 'Source',
        arrived: 'Arrived',
        status: 'Status'
      },
      errors: {
        fetchUser: 'Failed to fetch user data. Please login again.',
        loadShipments: 'Unable to load shipments.'
      }
    },
    statuses: {
      delivered: 'Delivery Confirmation',
      collecting: 'Collecting Co-pay',
      copay: 'Co-pay Assistant or Foundation Assistant',
      insuranceVerification: 'Insurance Verification & Authorization',
      intake: 'Intake Processing',
      dataEntry: 'Data Entry',
      confirmed: 'Confirmed Prescription Received',
      production: 'Production',
      unknown: 'Unknown',
    }

  },
  es: {
     header: {
      doctorLogin: 'Login Médico'
      },
      dashboard: {
        greeting: "Hola, {name}",
        subtext: "Sigue tu actividad y progreso abajo.",
        cards: {
          doctors: {
            title: "Doctores activos",
            linkText: "Ver doctores"
          },
          prescriptions: {
            title: "Recetas de hoy",
            linkText: "Ver todas"
          },
          completed: {
            title: "Completadas hoy",
            linkText: "Ver completadas"
          },
          month: {
            title: "Este mes",
            linkText: "Estadísticas mensuales"
          }
        }
      },
      settings: {
        title: 'Configuración',
        caretend: 'API de CareTend',
        clientId: 'ID del Cliente',
        secretKey: 'Clave Secreta',
        placeholderClientId: 'Ingrese el ID del Cliente',
        placeholderSecretKey: 'Ingrese la Clave Secreta',
        save: 'Guardar',
        update: 'Actualizar',
        delete: 'Eliminar',
        requiredClientId: 'El ID del Cliente es obligatorio',
        requiredSecret: 'La Clave Secreta es obligatoria',
        successSave: 'Credenciales guardadas',
        successUpdate: 'Credenciales actualizadas',
        successDelete: 'Credenciales eliminadas',
        errorSave: 'Error al guardar las credenciales',
        errorDelete: 'Error al eliminar las credenciales',
        languageTitle: 'Selecciona idioma'
      },
      chart: {
        orders: 'Pedidos',
        earnings: 'Ganancias',
        refunds: 'Reembolsos',
        months: ['May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
      },
      pieChart: {
        title: 'Prescripciones completadas por %',
        labels: ['Lisinopril', 'Levotiroxina', 'Atorvastatina', 'Metformina']
      },
      footer: {
        contact: 'Contáctanos',
        terms: 'Términos del Servicio',
        copyright: 'DeliverIt Pharmacy' // Keep brand name untranslated
      },
      layout: {
        menu: {
          dashboard: 'Panel',
          admins: 'Administradores',
          doctors: 'Doctores',
          profile: 'Perfil',
          settings: 'Configuración',
          referral: 'Formularios de Referencia',
          logout: 'Cerrar sesión'
        },
        partners: 'En asociación con',
        logoutSuccess: 'Sesión cerrada con éxito',
        logoutError: 'Error al cerrar sesión'
      },
      adminUsers: {
        title: 'Administrar Administradores',
        searchPlaceholder: 'Buscar usuarios...',
        addUser: 'Agregar Usuario',
        editUser: 'Editar Usuario',
        addNewAdmin: 'Agregar Nuevo Administrador',
        name: 'Nombre',
        email: 'Correo electrónico',
        password: 'Contraseña',
        confirmPassword: 'Confirmar Contraseña',
        save: 'Guardar',
        cancel: 'Cancelar',
        edit: 'Editar',
        delete: 'Eliminar',
        deleteTitle: 'Eliminar Usuario',
        deleteConfirm: '¿Estás seguro de que deseas eliminar este usuario?',
        deleteSuccess: '¡Usuario eliminado exitosamente!',
        addSuccess: '¡Usuario agregado exitosamente!',
        updateSuccess: '¡Usuario actualizado exitosamente!',
        errorSave: 'Error al guardar el usuario',
        errorDelete: 'Error al eliminar el usuario',
        errorLoad: 'Error al cargar usuarios',
        validation: {
          name: 'El nombre es obligatorio',
          email: 'El correo electrónico es obligatorio',
          invalidEmail: 'Correo electrónico inválido',
          passwordLength: 'La contraseña debe tener al menos 6 caracteres',
          passwordMatch: 'Las contraseñas no coinciden'
        }
      },
      doctors: {
        title: 'Administrar Doctores',
        searchPlaceholder: 'Buscar doctores...',
        add: 'Agregar Doctor',
        edit: 'Editar Doctor',
        addNew: 'Agregar Nuevo Doctor',
        name: 'Nombre',
        email: 'Correo electrónico',
        sheetId: 'Identificador de Hoja',
        password: 'Contraseña',
        confirmPassword: 'Confirmar Contraseña',
        save: 'Guardar',
        cancel: 'Cancelar',
        prescriptions: 'Recetas',
        delete: 'Eliminar',
        deleteTitle: 'Eliminar Usuario',
        deleteConfirm: '¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.',
        deleteSuccess: '¡Usuario eliminado exitosamente!',
        addSuccess: '¡Usuario agregado exitosamente!',
        updateSuccess: '¡Usuario actualizado exitosamente!',
        errorSave: 'Error al guardar el usuario',
        errorDelete: 'Error al eliminar el usuario',
        errorLoad: 'Error al cargar usuarios',
        validation: {
          name: 'El nombre es obligatorio',
          email: 'El correo es obligatorio',
          invalidEmail: 'Correo inválido',
          sheet: 'El identificador de hoja es obligatorio',
          passwordLength: 'La contraseña debe tener al menos 6 caracteres',
          passwordMatch: 'Las contraseñas no coinciden'
        }
      },
      profile: {
        title: 'Tu Perfil',
        name: 'Nombre',
        email: 'Correo electrónico',
        newPassword: 'Nueva contraseña',
        confirmPassword: 'Confirmar contraseña',
        placeholder: {
          name: 'Tu nombre',
          email: 'Tu correo electrónico',
          newPassword: 'Nueva contraseña',
          confirmPassword: 'Confirmar contraseña'
        },
        update: 'Actualizar Perfil',
        success: '¡Perfil actualizado con éxito!',
        error: 'Error al actualizar el perfil. Por favor revisa el formulario.',
        fetchError: 'Error al obtener los datos del usuario',
        validation: {
          name: 'El nombre es obligatorio',
          email: 'El correo electrónico es obligatorio',
          invalidEmail: 'Formato de correo electrónico inválido',
          passwordMin: 'La contraseña debe tener al menos 6 caracteres',
          passwordMatch: 'Las contraseñas no coinciden'
        }
      },
      dashboardPrescription: {
        profileTitle: 'Tu Perfil',
        currentlyViewing: '',
        viewing: 'Actualmente estás viendo a',
        prescriptionRefreshNote: 'Estas recetas están vinculadas a este doctor y se actualizan cada 30 minutos desde nuestro sistema.',
        needHelp: '¿Necesitas ayuda? Contáctanos:',
        phone: 'Teléfono',
        fax: 'Fax',
        goBack: 'Regresar',
        addPrescription: 'Agregar Receta',
        editPrescription: 'Editar Receta',
        createPrescription: 'Crear Receta',
        patientName: 'Nombre del Paciente',
        dob: 'Fecha de Nacimiento',
        dobPlaceHolder: 'Selecciona una Fecha',
        prescriptionName: 'Nombre de la Receta',
        source: 'Fuente',
        insurance: 'Seguro',
        city: 'Ciudad',
        status: 'Estado',
        arrivedToOffice: 'Fecha de Llegada a la Oficina',
        cancel: 'Cancelar',
        save: 'Guardar',
        searchPlaceholder: 'Buscar recetas o pacientes...',
        edit: 'Editar',
        delivered: 'Confirmación de Entrega',
        collecting: 'Cobro de Co-pago',
        copay: 'Asistencia de Co-pago o Fundación',
        insuranceVerification: 'Verificación y Autorización de Seguro',
        intake: 'Procesamiento de Ingreso',
        dataEntry: 'Ingreso de Datos',
        confirmed: 'Receta Confirmada Recibida',
        production: 'Producción',
        unknown: 'Desconocido',
        table: {
          id: 'ID',
          patient: 'Paciente',
          prescription: 'Receta',
          dob: 'Fecha de Nacimiento',
          insurance: 'Seguro',
          city: 'Ciudad',
          source: 'Fuente',
          arrived: 'Fecha de Llegada',
          status: 'Estado',
          actions: 'Acciones',
          edit: 'Editar'
        }
    },
    emailVerified: {
      title: 'Correo Verificado',
      message: 'Tu correo ha sido verificado exitosamente. Ya puedes iniciar sesión.',
      button: 'Ir al Inicio de Sesión'
    },
    forgot: {
      title: 'Restablecer Contraseña',
      emailLabel: 'Correo Electrónico',
      emailPlaceholder: 'Ingresa tu correo registrado',
      sendLink: 'Enviar Enlace de Restablecimiento',
      backToLogin: 'Volver al Inicio de Sesión'
    },
    home: {
      welcome: "Bienvenido al Portal para Médicos de DeliverIt",
      subtitle: "Optimiza la gestión de recetas y seguimientos con integración en tiempo real.",
      login: "Ingreso del Médico",
      toolsTitle: "Herramientas Poderosas para una Atención Más Inteligente",
      toolsDescription: "Monitorea recetas, referencias y estados de pacientes en tiempo real — todo desde una sola plataforma intuitiva para médicos.",
      features: {
        statusTitle: "Estado de Recetas",
        statusDesc: "Consulta y gestiona recetas activas, inactivas o en proceso — actualizado en tiempo real mediante CaredAPI.",
        referralTitle: "Flujo de Referencias",
        referralDesc: "Mantente informado sobre cada paso de las referencias — desde la Verificación de Beneficios hasta la Aprobación o Denegación.",
        patientTitle: "Estado del Paciente",
        patientDesc: "Haz seguimiento de la activación del paciente manual o automáticamente. Visualiza su estado actual."
      },
      faq: {
        title: "Preguntas Frecuentes",
        description: "Si no encuentras una respuesta aquí, contáctanos o envíanos un correo. ¡Te responderemos pronto!",
        email: "Envíanos un Correo",
        general: "Preguntas Generales",
        q1: "¿Cómo puedo rastrear el estado de las recetas en tiempo real?",
        a1: "Nuestra plataforma se sincroniza con CaredAPI para ofrecerte actualizaciones en tiempo real.",
        q2: "¿Qué etapas incluye el flujo de referencias?",
        a2: "Incluye Verificación de Beneficios, Aprobación o Denegación de Seguro, entre otros pasos clave.",
        q3: "¿Puedo ver si un paciente ha sido activado?",
        a3: "Sí, puedes hacerlo manual o automáticamente desde el portal.",
        q4: "¿Cómo se integra el sistema con CaredAPI?",
        a4: "Extrae actualizaciones de recetas en tiempo real, sin necesidad de herramientas externas.",
        q5: "¿Puedo monitorear varios estados de pacientes desde un solo panel?",
        a5: "Sí, puedes ver todos los estados desde el panel de control para médicos.",
        q6: "¿Qué tipos de estado de recetas son compatibles?",
        a6: "Actualmente soportamos estados Activo, Inactivo y En Proceso.",
        q7: "¿Cada cuánto se actualiza la información del paciente?",
        a7: "Se actualiza en tiempo real conforme ocurren los eventos.",
        privacy: "Privacidad y Seguridad",
        q8: "¿La plataforma cumple con HIPAA?",
        a8: "Sí, cumple completamente con HIPAA y protege la información.",
        q9: "¿Cómo se protege la información del paciente?",
        a9: "Los datos están cifrados en tránsito y reposo.",
        q10: "¿Quién puede ver los datos médicos?",
        a10: "Solo médicos autorizados o personal aprobado con permisos específicos.",
        q11: "¿Se almacenan datos fuera de EE.UU.?",
        a11: "No. Todo se almacena y procesa dentro de EE.UU."
      },
      contact: {
        title: "Contáctanos",
        email: "Correo: doctors@deliverit.health",
        phone: "Teléfono: (555) 123-4567"
      }
    },
    login: {
      title: "Iniciar Sesión",
      email: "Correo Electrónico",
      emailPlaceholder: "Ingresa tu correo",
      password: "Contraseña",
      passwordPlaceholder: "Ingresa tu contraseña",
      rememberMe: "Recuérdame",
      forgotPassword: "¿Olvidaste tu contraseña?",
      signIn: "Iniciar sesión",
      loginMsg: {
        emailMsg: "Por favor, ingresa tu correo electrónico",
        emailMsgValid: "Por favor, ingresa una dirección de correo válida",
        emailPwd: "Por favor, ingresa tu contraseña",
        emailPwdCh: "La contraseña debe tener al menos 6 caracteres",
      },
      errorMessage: "Error al iniciar sesión, por favor verifica tus credenciales"
    },
    userDash: {
      greeting: 'Hola, {name}',
      subtext: 'Aquí puedes ver el estado de tus recetas. Las actualizaciones se obtienen de nuestro sistema interno cada 30 minutos para garantizar la exactitud.',
      needHelp: '¿Necesitas ayuda? Contáctanos:',
      phone: 'Teléfono',
      fax: 'Fax',
      searchPlaceholder: 'Buscar recetas o pacientes...',
      table: {
        patient: 'Paciente',
        prescription: 'Receta',
        dob: 'Fecha de nacimiento',
        insurance: 'Seguro',
        city: 'Ciudad',
        source: 'Fuente',
        arrived: 'Fecha de llegada',
        status: 'Estado'
      },
      errors: {
        fetchUser: 'Error al obtener los datos del usuario. Por favor, inicia sesión nuevamente.',
        loadShipments: 'No se pueden cargar los envíos.'
      }
    },
    statuses: {
      delivered: 'Confirmación de Entrega',
      collecting: 'Cobro de Co-pago',
      copay: 'Asistencia de Co-pago o Fundación',
      insuranceVerification: 'Verificación y Autorización de Seguro',
      intake: 'Procesamiento de Ingreso',
      dataEntry: 'Ingreso de Datos',
      confirmed: 'Receta Confirmada Recibida',
      production: 'Producción',
      unknown: 'Desconocido',
    }

  }
}

const savedLang = localStorage.getItem('language') || 'en'

const i18n = createI18n({
  legacy: false,
  locale: savedLang,        // ✅ Sets the starting language
  fallbackLocale: 'en',
  messages
})


export default i18n
