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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = readFromConsumerApi;
function readFromConsumerApi(key) {
    return function () {
        if (window['@Neos:HostPluginAPI'] && window['@Neos:HostPluginAPI']['@' + key]) {
            var _window$NeosHostPlu;

            return (_window$NeosHostPlu = window['@Neos:HostPluginAPI'])['@' + key].apply(_window$NeosHostPlu, arguments);
        }

        throw new Error('You are trying to read from a consumer api that hasn\'t been initialized yet!');
    };
}

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _readFromConsumerApi = __webpack_require__(0);

var _readFromConsumerApi2 = _interopRequireDefault(_readFromConsumerApi);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

module.exports = (0, _readFromConsumerApi2.default)('vendor')().plow;

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__(3);

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _neosUiExtensibility = __webpack_require__(4);

var _neosUiExtensibility2 = _interopRequireDefault(_neosUiExtensibility);

var _PlaceholderInsertDropdown = __webpack_require__(8);

var _PlaceholderInsertDropdown2 = _interopRequireDefault(_PlaceholderInsertDropdown);

var _placeholderInsertPlugin = __webpack_require__(14);

var _placeholderInsertPlugin2 = _interopRequireDefault(_placeholderInsertPlugin);

var _plowJs = __webpack_require__(1);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var addPlugin = function addPlugin(Plugin, isEnabled) {
  return function (ckEditorConfiguration, options) {
    if (!isEnabled || isEnabled(options.editorOptions, options)) {
      ckEditorConfiguration.plugins = ckEditorConfiguration.plugins || [];
      return (0, _plowJs.$add)("plugins", Plugin, ckEditorConfiguration);
    }
    return ckEditorConfiguration;
  };
};

(0, _neosUiExtensibility2.default)("Neos.Form.Builder:PlaceholderInsert", {}, function (globalRegistry) {
  var richtextToolbar = globalRegistry.get("ckEditor5").get("richtextToolbar");
  richtextToolbar.set("placeholderInsertt", {
    component: _PlaceholderInsertDropdown2.default,
    isVisible: (0, _plowJs.$get)("formatting.placeholderInsert")
  });

  var config = globalRegistry.get("ckEditor5").get("config");
  config.set("placeholderInsert", addPlugin(_placeholderInsertPlugin2.default, (0, _plowJs.$get)("formatting.placeholderInsert")));
});

/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.createConsumerApi = undefined;

var _createConsumerApi = __webpack_require__(5);

var _createConsumerApi2 = _interopRequireDefault(_createConsumerApi);

var _readFromConsumerApi = __webpack_require__(0);

var _readFromConsumerApi2 = _interopRequireDefault(_readFromConsumerApi);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = (0, _readFromConsumerApi2.default)('manifest');
exports.createConsumerApi = _createConsumerApi2.default;

/***/ }),
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = createConsumerApi;

var _package = __webpack_require__(6);

var _manifest = __webpack_require__(7);

var _manifest2 = _interopRequireDefault(_manifest);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var createReadOnlyValue = function createReadOnlyValue(value) {
    return {
        value: value,
        writable: false,
        enumerable: false,
        configurable: true
    };
};

function createConsumerApi(manifests, exposureMap) {
    var api = {};

    Object.keys(exposureMap).forEach(function (key) {
        Object.defineProperty(api, key, createReadOnlyValue(exposureMap[key]));
    });

    Object.defineProperty(api, '@manifest', createReadOnlyValue((0, _manifest2.default)(manifests)));

    Object.defineProperty(window, '@Neos:HostPluginAPI', createReadOnlyValue(api));
    Object.defineProperty(window['@Neos:HostPluginAPI'], 'VERSION', createReadOnlyValue(_package.version));
}

/***/ }),
/* 6 */
/***/ (function(module, exports) {

module.exports = {"name":"@neos-project/neos-ui-extensibility","version":"1.4.1","description":"Extensibility mechanisms for the Neos CMS UI","main":"./src/index.js","scripts":{"prebuild":"check-dependencies && yarn clean","test":"yarn jest -- -w 2 --coverage","test:watch":"yarn jest -- --watch","build":"exit 0","build:watch":"exit 0","clean":"rimraf ./lib ./dist","lint":"eslint src","jest":"NODE_ENV=test jest"},"devDependencies":{"@neos-project/babel-preset-neos-ui":"1.4.1","@neos-project/jest-preset-neos-ui":"1.4.1"},"dependencies":{"@neos-project/build-essentials":"1.4.1","@neos-project/positional-array-sorter":"1.4.1","babel-core":"^6.13.2","babel-eslint":"^7.1.1","babel-loader":"^7.1.2","babel-plugin-transform-decorators-legacy":"^1.3.4","babel-plugin-transform-object-rest-spread":"^6.20.1","babel-plugin-webpack-alias":"^2.1.1","babel-preset-es2015":"^6.13.2","babel-preset-react":"^6.3.13","babel-preset-stage-0":"^6.3.13","chalk":"^1.1.3","css-loader":"^0.28.4","file-loader":"^1.1.5","json-loader":"^0.5.4","postcss-loader":"^2.0.10","react-dev-utils":"^0.5.0","style-loader":"^0.21.0"},"bin":{"neos-react-scripts":"./bin/neos-react-scripts.js"},"jest":{"preset":"@neos-project/jest-preset-neos-ui"}}

/***/ }),
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

exports.default = function (manifests) {
    return function (identifier, options, bootstrap) {
        manifests.push(_defineProperty({}, identifier, {
            options: options,
            bootstrap: bootstrap
        }));
    };
};

/***/ }),
/* 8 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = exports.parentNodeContextPath = undefined;

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _dec, _dec2, _class;

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

var _reactRedux = __webpack_require__(9);

var _reactUiComponents = __webpack_require__(10);

var _react = __webpack_require__(11);

var _react2 = _interopRequireDefault(_react);

var _neosUiDecorators = __webpack_require__(13);

var _plowJs = __webpack_require__(1);

var _neosUiReduxStore = __webpack_require__(16);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var parentNodeContextPath = exports.parentNodeContextPath = function parentNodeContextPath(contextPath) {
  if (typeof contextPath !== "string") {
    console.error("`contextPath` must be a string!"); // tslint:disable-line
    return null;
  }

  var _contextPath$split = contextPath.split("@"),
      _contextPath$split2 = _slicedToArray(_contextPath$split, 2),
      path = _contextPath$split2[0],
      context = _contextPath$split2[1];

  if (path.length === 0) {
    // We are at top level; so there is no parent anymore!
    return null;
  }

  return path.substr(0, path.lastIndexOf("/")) + "@" + context;
};

var PlaceholderInsertDropdown = (_dec = (0, _reactRedux.connect)((0, _plowJs.$transform)({
  nodesByContextPath: _neosUiReduxStore.selectors.CR.Nodes.nodesByContextPathSelector,
  focusedNode: _neosUiReduxStore.selectors.CR.Nodes.focusedSelector
})), _dec2 = (0, _neosUiDecorators.neos)(function (globalRegistry) {
  return {
    i18nRegistry: globalRegistry.get("i18n"),
    nodeTypeRegistry: globalRegistry.get("@neos-project/neos-ui-contentrepository")
  };
}), _dec(_class = _dec2(_class = function (_PureComponent) {
  _inherits(PlaceholderInsertDropdown, _PureComponent);

  function PlaceholderInsertDropdown() {
    var _ref;

    var _temp, _this, _ret;

    _classCallCheck(this, PlaceholderInsertDropdown);

    for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    return _ret = (_temp = (_this = _possibleConstructorReturn(this, (_ref = PlaceholderInsertDropdown.__proto__ || Object.getPrototypeOf(PlaceholderInsertDropdown)).call.apply(_ref, [this].concat(args))), _this), _this.handleOnSelect = function (value) {
      _this.props.executeCommand("placeholderInsert", value);
    }, _temp), _possibleConstructorReturn(_this, _ret);
  }

  _createClass(PlaceholderInsertDropdown, [{
    key: "render",
    value: function render() {
      var _this2 = this;

      var _parentNodeContextPat = parentNodeContextPath(parentNodeContextPath(this.props.focusedNode.contextPath)).split("@"),
          _parentNodeContextPat2 = _slicedToArray(_parentNodeContextPat, 2),
          formPath = _parentNodeContextPat2[0],
          workspace = _parentNodeContextPat2[1];

      var elementsPath = formPath + "/elements@" + workspace;

      var elementsNode = this.props.nodesByContextPath[elementsPath];
      if (!elementsNode) {
        return null;
      }
      var options = elementsNode.children.map(function (node) {
        return _this2.props.nodesByContextPath[node.contextPath];
      }).map(function (node) {
        return {
          value: node.properties.identifier || node.identifier,
          label: node.properties.label || node.properties.identifier || node.identifier
        };
      });

      if (options.length === 0) {
        return null;
      }

      var placeholderLabel = this.props.i18nRegistry.translate("Neos.Form.Builder:Main:placeholder", "Insert placeholder");

      return _react2.default.createElement(_reactUiComponents.SelectBox, {
        placeholder: placeholderLabel,
        options: options,
        onValueChange: this.handleOnSelect,
        value: null
      });
    }
  }]);

  return PlaceholderInsertDropdown;
}(_react.PureComponent)) || _class) || _class);
exports.default = PlaceholderInsertDropdown;

/***/ }),
/* 9 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _readFromConsumerApi = __webpack_require__(0);

var _readFromConsumerApi2 = _interopRequireDefault(_readFromConsumerApi);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

module.exports = (0, _readFromConsumerApi2.default)('vendor')().reactRedux;

/***/ }),
/* 10 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _readFromConsumerApi = __webpack_require__(0);

var _readFromConsumerApi2 = _interopRequireDefault(_readFromConsumerApi);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

module.exports = (0, _readFromConsumerApi2.default)('NeosProjectPackages')().ReactUiComponents;

/***/ }),
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _readFromConsumerApi = __webpack_require__(0);

var _readFromConsumerApi2 = _interopRequireDefault(_readFromConsumerApi);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

module.exports = (0, _readFromConsumerApi2.default)('vendor')().React;

/***/ }),
/* 12 */,
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _readFromConsumerApi = __webpack_require__(0);

var _readFromConsumerApi2 = _interopRequireDefault(_readFromConsumerApi);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

module.exports = (0, _readFromConsumerApi2.default)('NeosProjectPackages')().NeosUiDecorators;

/***/ }),
/* 14 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = undefined;

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _ckeditor5Exports = __webpack_require__(15);

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var PlaceholderInsertCommand = function (_Command) {
  _inherits(PlaceholderInsertCommand, _Command);

  function PlaceholderInsertCommand() {
    _classCallCheck(this, PlaceholderInsertCommand);

    return _possibleConstructorReturn(this, (PlaceholderInsertCommand.__proto__ || Object.getPrototypeOf(PlaceholderInsertCommand)).apply(this, arguments));
  }

  _createClass(PlaceholderInsertCommand, [{
    key: "execute",
    value: function execute(value) {
      var model = this.editor.model;
      var doc = model.document;
      var selection = doc.selection;
      var placeholder = new _ckeditor5Exports.ModelText("{" + value + "}");
      model.insertContent(placeholder, selection);
    }
  }]);

  return PlaceholderInsertCommand;
}(_ckeditor5Exports.Command);

var PlaceholderInsertPlugin = function (_Plugin) {
  _inherits(PlaceholderInsertPlugin, _Plugin);

  function PlaceholderInsertPlugin() {
    _classCallCheck(this, PlaceholderInsertPlugin);

    return _possibleConstructorReturn(this, (PlaceholderInsertPlugin.__proto__ || Object.getPrototypeOf(PlaceholderInsertPlugin)).apply(this, arguments));
  }

  _createClass(PlaceholderInsertPlugin, [{
    key: "init",
    value: function init() {
      var editor = this.editor;

      editor.commands.add("placeholderInsert", new PlaceholderInsertCommand(this.editor));
    }
  }], [{
    key: "pluginName",
    get: function get() {
      return "PlaceholderInsert";
    }
  }]);

  return PlaceholderInsertPlugin;
}(_ckeditor5Exports.Plugin);

exports.default = PlaceholderInsertPlugin;

/***/ }),
/* 15 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _readFromConsumerApi = __webpack_require__(0);

var _readFromConsumerApi2 = _interopRequireDefault(_readFromConsumerApi);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

module.exports = (0, _readFromConsumerApi2.default)('vendor')().CkEditor5;

/***/ }),
/* 16 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _readFromConsumerApi = __webpack_require__(0);

var _readFromConsumerApi2 = _interopRequireDefault(_readFromConsumerApi);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

module.exports = (0, _readFromConsumerApi2.default)('NeosProjectPackages')().NeosUiReduxStore;

/***/ })
/******/ ]);
//# sourceMappingURL=Plugin.js.map