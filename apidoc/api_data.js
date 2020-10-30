define({ "api": [
  {
    "type": "post",
    "url": "/admin/make-approved",
    "title": "Make Approve API",
    "name": "Make_Approve_API",
    "group": "Admin",
    "examples": [
      {
        "title": "JSON-Body:",
        "content": "{\n    \"userId\":\"4\"\n}",
        "type": "json"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "token",
            "description": "<p>Token</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "status",
            "description": "<p>Status of the request.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Message corresponding to request.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "controllers/users.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "/user/post-feedback",
    "title": "Post Feedback API",
    "name": "Post_Feedback_API",
    "group": "User",
    "examples": [
      {
        "title": "JSON-Body:",
        "content": "{\n    \"mechanicId\":\"22\",\n    \"feedback\":\"3\",\n    \"comment\":\"good work\"\n}",
        "type": "json"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "token",
            "description": "<p>Token</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "status",
            "description": "<p>Status of the request.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Message corresponding to request.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "controllers/users.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "/common/list-mechanic",
    "title": "List Mechanic API",
    "name": "List_Mechanic_API",
    "group": "User_and_Admin",
    "examples": [
      {
        "title": "JSON-Body:",
        "content": "{\n    \"limit\":\"10\",\n    \"offset\":\"0\",\n    \"name\":\"imran\"\n}",
        "type": "json"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "token",
            "description": "<p>Token</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "status",
            "description": "<p>Status of the request.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Message corresponding to request.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "controllers/users.php",
    "groupTitle": "User_and_Admin"
  },
  {
    "type": "post",
    "url": "/common/change-password",
    "title": "Change Password API",
    "name": "Change_Password_API",
    "group": "User_and_Mechanic",
    "examples": [
      {
        "title": "JSON-Body:",
        "content": "{\n    \"oldPassword\":\"123\",\n    \"newPassword\":\"444\"\n}",
        "type": "json"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "token",
            "description": "<p>Token</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "status",
            "description": "<p>Status of the request.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Message corresponding to request.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "controllers/users.php",
    "groupTitle": "User_and_Mechanic"
  },
  {
    "type": "post",
    "url": "/common/image-upload",
    "title": "Image Upload API",
    "name": "Image_Upload_API",
    "group": "User_and_Mechanic",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "file",
            "optional": false,
            "field": "imageFile",
            "description": "<p>Image File</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "status",
            "description": "<p>Status of the request.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Message corresponding to request.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "controllers/users.php",
    "groupTitle": "User_and_Mechanic"
  },
  {
    "type": "post",
    "url": "/common/login",
    "title": "Login API",
    "name": "Login_API",
    "group": "User_and_Mechanic",
    "examples": [
      {
        "title": "JSON-Body:",
        "content": "{\n    \"email\":\"mr.imran@gmail.com\",\n    \"password\":\"123\"\n}",
        "type": "json"
      }
    ],
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "status",
            "description": "<p>Status of the request.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Message corresponding to request.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "controllers/users.php",
    "groupTitle": "User_and_Mechanic"
  },
  {
    "type": "post",
    "url": "/common/logout",
    "title": "Logout API",
    "name": "Logout_API",
    "group": "User_and_Mechanic",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "token",
            "description": "<p>Token</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "status",
            "description": "<p>Status of the request.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Message corresponding to request.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "controllers/users.php",
    "groupTitle": "User_and_Mechanic"
  },
  {
    "type": "post",
    "url": "/common/sign-up",
    "title": "Sign Up API",
    "name": "Sign_Up_API",
    "group": "User_and_Mechanic",
    "examples": [
      {
        "title": "JSON-Body:",
        "content": " {\n    \"name\":\"Mr Imran\",\n    \"contact\":\"123456780\",\n    \"password\":\"123\",\n    \"role\":\"2\",\n    \"email\":\"mr.imran@gmail.com\",\n    \"address\":\"karachi Pakistan\",\n    \"speciality\":\"denter master\",\n    \"description\":\"I am a good Denter\",\n    \"lat\":\"24.45657899\",\n    \"lng\":\"67.09876754\"\n}",
        "type": "json"
      }
    ],
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "status",
            "description": "<p>Status of the request.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Message corresponding to request.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "controllers/users.php",
    "groupTitle": "User_and_Mechanic"
  },
  {
    "type": "post",
    "url": "/common/update-profile",
    "title": "Update Profile API",
    "name": "Update_Profile_API",
    "group": "User_and_Mechanic",
    "examples": [
      {
        "title": "JSON-Body:",
        "content": "{\n    \"name\":\"Imran Bhai\",\n    \"contact\":\"98918227342\",\n    \"speciality\":\"Painter\",\n    \"description\":\"I am a good painter\",\n    \"profileImage\":\"cdn/images/63971603221353.png\"\n}",
        "type": "json"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "token",
            "description": "<p>Token</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "status",
            "description": "<p>Status of the request.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Message corresponding to request.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "controllers/users.php",
    "groupTitle": "User_and_Mechanic"
  },
  {
    "type": "post",
    "url": "/common/verify-login",
    "title": "Verify Login API",
    "name": "Verify_Login_API",
    "group": "User_and_Mechanic",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "token",
            "description": "<p>Token</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "status",
            "description": "<p>Status of the request.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Message corresponding to request.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "controllers/users.php",
    "groupTitle": "User_and_Mechanic"
  }
] });
