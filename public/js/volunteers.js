/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./Resources/assets/js/app.js":
/*!************************************!*\
  !*** ./Resources/assets/js/app.js ***!
  \************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_VolunteerApp_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/VolunteerApp.vue */ "./Resources/assets/js/components/VolunteerApp.vue");
/* harmony import */ var _components_VolunteerList_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/VolunteerList.vue */ "./Resources/assets/js/components/VolunteerList.vue");

window.Vue.component('volunteer-app', _components_VolunteerApp_vue__WEBPACK_IMPORTED_MODULE_0__["default"]);

window.Vue.component('volunteer-list', _components_VolunteerList_vue__WEBPACK_IMPORTED_MODULE_1__["default"]);
var app = new Vue({
  el: '#app'
});

/***/ }),

/***/ "./Resources/assets/js/components/VolunteerApp.vue":
/*!*********************************************************!*\
  !*** ./Resources/assets/js/components/VolunteerApp.vue ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _VolunteerApp_vue_vue_type_template_id_a4c98f64___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./VolunteerApp.vue?vue&type=template&id=a4c98f64& */ "./Resources/assets/js/components/VolunteerApp.vue?vue&type=template&id=a4c98f64&");
/* harmony import */ var _VolunteerApp_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./VolunteerApp.vue?vue&type=script&lang=js& */ "./Resources/assets/js/components/VolunteerApp.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _VolunteerApp_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _VolunteerApp_vue_vue_type_template_id_a4c98f64___WEBPACK_IMPORTED_MODULE_0__["render"],
  _VolunteerApp_vue_vue_type_template_id_a4c98f64___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "Resources/assets/js/components/VolunteerApp.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./Resources/assets/js/components/VolunteerApp.vue?vue&type=script&lang=js&":
/*!**********************************************************************************!*\
  !*** ./Resources/assets/js/components/VolunteerApp.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VolunteerApp_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./VolunteerApp.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./Resources/assets/js/components/VolunteerApp.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VolunteerApp_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./Resources/assets/js/components/VolunteerApp.vue?vue&type=template&id=a4c98f64&":
/*!****************************************************************************************!*\
  !*** ./Resources/assets/js/components/VolunteerApp.vue?vue&type=template&id=a4c98f64& ***!
  \****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VolunteerApp_vue_vue_type_template_id_a4c98f64___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./VolunteerApp.vue?vue&type=template&id=a4c98f64& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./Resources/assets/js/components/VolunteerApp.vue?vue&type=template&id=a4c98f64&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VolunteerApp_vue_vue_type_template_id_a4c98f64___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VolunteerApp_vue_vue_type_template_id_a4c98f64___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./Resources/assets/js/components/VolunteerList.vue":
/*!**********************************************************!*\
  !*** ./Resources/assets/js/components/VolunteerList.vue ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _VolunteerList_vue_vue_type_template_id_130562c1___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./VolunteerList.vue?vue&type=template&id=130562c1& */ "./Resources/assets/js/components/VolunteerList.vue?vue&type=template&id=130562c1&");
/* harmony import */ var _VolunteerList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./VolunteerList.vue?vue&type=script&lang=js& */ "./Resources/assets/js/components/VolunteerList.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _VolunteerList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _VolunteerList_vue_vue_type_template_id_130562c1___WEBPACK_IMPORTED_MODULE_0__["render"],
  _VolunteerList_vue_vue_type_template_id_130562c1___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "Resources/assets/js/components/VolunteerList.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./Resources/assets/js/components/VolunteerList.vue?vue&type=script&lang=js&":
/*!***********************************************************************************!*\
  !*** ./Resources/assets/js/components/VolunteerList.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VolunteerList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./VolunteerList.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./Resources/assets/js/components/VolunteerList.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VolunteerList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./Resources/assets/js/components/VolunteerList.vue?vue&type=template&id=130562c1&":
/*!*****************************************************************************************!*\
  !*** ./Resources/assets/js/components/VolunteerList.vue?vue&type=template&id=130562c1& ***!
  \*****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VolunteerList_vue_vue_type_template_id_130562c1___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./VolunteerList.vue?vue&type=template&id=130562c1& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./Resources/assets/js/components/VolunteerList.vue?vue&type=template&id=130562c1&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VolunteerList_vue_vue_type_template_id_130562c1___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VolunteerList_vue_vue_type_template_id_130562c1___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./Resources/assets/sass/app.scss":
/*!****************************************!*\
  !*** ./Resources/assets/sass/app.scss ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./Resources/assets/js/components/VolunteerApp.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./Resources/assets/js/components/VolunteerApp.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      volunteer: null
    };
  },
  mounted: function mounted() {},
  methods: {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./Resources/assets/js/components/VolunteerList.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./Resources/assets/js/components/VolunteerList.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      loaded: false,
      scope: 'active',
      volunteers: [],
      error: null
    };
  },
  mounted: function mounted() {
    this.refresh();
  },
  watch: {
    scope: function scope(val, oldVal) {
      this.loadData(val);
    }
  },
  methods: {
    refresh: function refresh() {
      this.loadData(this.scope);
    },
    loadData: function loadData(scope) {
      var _this = this;

      this.loaded = false;
      this.error = null;
      axios.get('api/volunteers?scope=' + scope).then(function (res) {
        _this.volunteers = res.data.data;
      })["catch"](function (err) {
        _this.error = err;
      }).then(function () {
        _this.loaded = true;
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./Resources/assets/js/components/VolunteerApp.vue?vue&type=template&id=a4c98f64&":
/*!**********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./Resources/assets/js/components/VolunteerApp.vue?vue&type=template&id=a4c98f64& ***!
  \**********************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _vm.volunteer != null
        ? _c("div", [_vm._v("\n        Volunteer\n    ")])
        : _vm._e(),
      _vm._v(" "),
      _c("volunteer-list")
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./Resources/assets/js/components/VolunteerList.vue?vue&type=template&id=130562c1&":
/*!***********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./Resources/assets/js/components/VolunteerList.vue?vue&type=template&id=130562c1& ***!
  \***********************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("div", { staticClass: "row mb-3 mb-sm-0" }, [
      _c("div", { staticClass: "col col-auto" }, [
        _c(
          "div",
          {
            staticClass: "btn-group btn-group-sm mb-3",
            attrs: { role: "group", "aria-label": "Scopes" }
          },
          [
            _c(
              "button",
              {
                staticClass: "btn btn-sm",
                class: {
                  "btn-dark": _vm.scope == "active",
                  "btn-secondary": _vm.scope != "active"
                },
                on: {
                  click: function($event) {
                    _vm.scope = "active"
                  }
                }
              },
              [_vm._v("Active")]
            ),
            _vm._v(" "),
            _c(
              "button",
              {
                staticClass: "btn btn-sm",
                class: {
                  "btn-dark": _vm.scope == "future",
                  "btn-secondary": _vm.scope != "future"
                },
                on: {
                  click: function($event) {
                    _vm.scope = "future"
                  }
                }
              },
              [_vm._v("Future")]
            ),
            _vm._v(" "),
            _c(
              "button",
              {
                staticClass: "btn btn-sm",
                class: {
                  "btn-dark": _vm.scope == "previous",
                  "btn-secondary": _vm.scope != "previous"
                },
                on: {
                  click: function($event) {
                    _vm.scope = "previous"
                  }
                }
              },
              [_vm._v("Previous")]
            ),
            _vm._v(" "),
            _c(
              "button",
              {
                staticClass: "btn btn-sm",
                class: {
                  "btn-dark": _vm.scope == "applied",
                  "btn-secondary": _vm.scope != "applied"
                },
                on: {
                  click: function($event) {
                    _vm.scope = "applied"
                  }
                }
              },
              [_vm._v("Applications")]
            )
          ]
        )
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "col col-auto" }, [
        _c(
          "button",
          {
            staticClass: "btn btn-sm btn-secondary",
            attrs: { disabled: !_vm.loaded },
            on: {
              click: function($event) {
                return _vm.refresh()
              }
            }
          },
          [
            _c("i", {
              staticClass: "fas fa-sync",
              class: { "fa-spin": !_vm.loaded }
            })
          ]
        )
      ])
    ]),
    _vm._v(" "),
    _vm.error != null
      ? _c("div", { staticClass: "alert alert-warning" }, [
          _vm._v("\n        " + _vm._s(_vm.error) + "\n    ")
        ])
      : _vm._e(),
    _vm._v(" "),
    !_vm.loaded
      ? _c("div", { staticClass: "text-center" }, [
          _vm._v("\n        Loading...\n    ")
        ])
      : _vm.volunteers.length > 0
      ? _c("div", { staticClass: "table-responsive" }, [
          _c(
            "table",
            {
              staticClass:
                "table table-sm table-bordered table-striped table-hover"
            },
            [
              _c("thead", [
                _c("tr", [
                  _c("th", [_vm._v("Name")]),
                  _vm._v(" "),
                  _c("th", [_vm._v("Nationality")]),
                  _vm._v(" "),
                  _c("th", [_vm._v("Age")]),
                  _vm._v(" "),
                  _c("th", [_vm._v("Gender")]),
                  _vm._v(" "),
                  _c("th", [_vm._v("Languages")]),
                  _vm._v(" "),
                  _vm.scope == "future" || _vm.scope == "applications"
                    ? _c("th", [_vm._v("Arrival")])
                    : _vm._e(),
                  _vm._v(" "),
                  _c("th", [_vm._v("Departure")]),
                  _vm._v(" "),
                  _vm.scope == "future" || _vm.scope == "applications"
                    ? _c("th", [_vm._v("Number of days")])
                    : _vm._e()
                ])
              ]),
              _vm._v(" "),
              _c(
                "tbody",
                _vm._l(_vm.volunteers, function(volunteer) {
                  return _c("tr", { key: volunteer.id }, [
                    _c("td", [
                      _vm._v(
                        _vm._s(volunteer.first_name) +
                          " " +
                          _vm._s(volunteer.last_name)
                      )
                    ]),
                    _vm._v(" "),
                    _c("td", [_vm._v(_vm._s(volunteer.nationality))]),
                    _vm._v(" "),
                    _c("td", [_vm._v(_vm._s(volunteer.age))]),
                    _vm._v(" "),
                    _c("td", [
                      _c("i", {
                        staticClass: "fas",
                        class: {
                          "fa-male": volunteer.gender == "m",
                          "fa-female": volunteer.gender == "f"
                        }
                      })
                    ]),
                    _vm._v(" "),
                    _c(
                      "td",
                      [
                        _vm._l(volunteer.languages, function(language) {
                          return [
                            _vm._v(
                              "\n                            " +
                                _vm._s(language)
                            ),
                            _c("br", { key: language })
                          ]
                        })
                      ],
                      2
                    ),
                    _vm._v(" "),
                    _vm.scope == "future" || _vm.scope == "applications"
                      ? _c("td", [_vm._v(_vm._s(volunteer.stay.arrival))])
                      : _vm._e(),
                    _vm._v(" "),
                    _c(
                      "td",
                      [
                        volunteer.stay.departure != null
                          ? [
                              _vm._v(
                                "\n                            " +
                                  _vm._s(volunteer.stay.departure) +
                                  "\n                        "
                              )
                            ]
                          : [
                              _vm._v(
                                "\n                            open-end\n                        "
                              )
                            ]
                      ],
                      2
                    ),
                    _vm._v(" "),
                    _vm.scope == "future" || _vm.scope == "applications"
                      ? _c("td", [_vm._v(_vm._s(volunteer.stay.num_days))])
                      : _vm._e()
                  ])
                }),
                0
              )
            ]
          ),
          _vm._v(" "),
          _c("p", [
            _c("small", [
              _vm._v(_vm._s(_vm.volunteers.length) + " volunteers in total")
            ])
          ])
        ])
      : _vm.error == null
      ? _c("div", { staticClass: "alert alert-info" }, [
          _vm._v("\n        No volunteers registrations found!\n    ")
        ])
      : _vm._e()
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js":
/*!********************************************************************!*\
  !*** ./node_modules/vue-loader/lib/runtime/componentNormalizer.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return normalizeComponent; });
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent (
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier, /* server only */
  shadowMode /* vue-cli only */
) {
  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (render) {
    options.render = render
    options.staticRenderFns = staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = 'data-v-' + scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = shadowMode
      ? function () { injectStyles.call(this, this.$root.$options.shadowRoot) }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}


/***/ }),

/***/ 0:
/*!***************************************************************************!*\
  !*** multi ./Resources/assets/js/app.js ./Resources/assets/sass/app.scss ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! C:\Users\Nicolas\devel\web\ohf-development\Modules\Volunteers\Resources\assets\js\app.js */"./Resources/assets/js/app.js");
module.exports = __webpack_require__(/*! C:\Users\Nicolas\devel\web\ohf-development\Modules\Volunteers\Resources\assets\sass\app.scss */"./Resources/assets/sass/app.scss");


/***/ })

/******/ });