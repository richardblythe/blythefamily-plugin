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
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/src/scripts/admin/blocks/_lyrics-section-label.js":
/*!******************************************************************!*\
  !*** ./assets/src/scripts/admin/blocks/_lyrics-section-label.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

(function (blocks, blockEditor, element) {
  var el = element.createElement;
  var RichText = blockEditor.RichText;
  var useBlockProps = blockEditor.useBlockProps;
  var createBlock = blocks.createBlock;
  blocks.registerBlockType('blythe/lyrics-section-label', {
    apiVersion: 2,
    title: 'Lyrics Section Label',
    icon: 'editor-ltr',
    category: 'design',
    attributes: {
      content: {
        type: 'array',
        source: 'children',
        selector: 'span'
      }
    },
    transforms: {
      from: [{
        type: 'block',
        blocks: ['core/paragraph', 'blythe/lyrics-section'],
        transform: function transform(attributes) {
          var lines = typeof attributes.content === 'string' ? attributes.content.split('<br>') : '';

          if (lines.length > 1) {
            var newBlocks = [];

            for (var i = 0; i < lines.length; i++) {
              newBlocks.push(createBlock(i == 0 ? 'blythe/lyrics-section-label' : 'blythe/lyrics-section', {
                content: lines[i]
              }));
            }

            return newBlocks;
          } //else


          return createBlock('blythe/lyrics-section', {
            content: attributes.content
          });
        }
      }],
      to: [{
        type: 'block',
        blocks: ['blythe/lyrics-section'],
        transform: function transform(attributes) {
          return createBlock('blythe/lyrics-section', {
            content: attributes.content
          });
        }
      }]
    },
    example: {
      attributes: {
        content: 'Hello World'
      }
    },
    edit: function edit(props) {
      var blockProps = useBlockProps();
      var content = props.attributes.content;

      function onChangeContent(newContent) {
        props.setAttributes({
          content: newContent
        });
      }

      function onSplitContent(value, isOriginal) {
        var block;
        block = createBlock(isOriginal ? 'blythe/lyrics-section-label' : 'blythe/lyrics-section', _objectSpread(_objectSpread({}, props.attributes), {}, {
          content: value
        }));

        if (isOriginal) {
          block.clientId = clientId;
        }

        return block;
      }

      return el(RichText, Object.assign(blockProps, {
        tagName: 'span',
        onChange: onChangeContent,
        placeholder: 'Lyrics section label...',
        keepPlaceholderOnFocus: true,
        multiline: false,
        onSplit: onSplitContent,
        onReplace: props.onReplace,
        onRemove: props.onReplace ? function () {
          return props.onReplace([]);
        } : undefined,
        value: content
      }));
    },
    save: function save(props) {
      var blockProps = useBlockProps.save();
      return el(RichText.Content, Object.assign(blockProps, {
        tagName: 'span',
        className: 'lyrics-section-label',
        value: props.attributes.content
      }));
    }
  });
})(window.wp.blocks, window.wp.blockEditor, window.wp.element);

/***/ }),

/***/ "./assets/src/scripts/admin/blocks/_lyrics-section.js":
/*!************************************************************!*\
  !*** ./assets/src/scripts/admin/blocks/_lyrics-section.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function (blocks, element, blockEditor) {
  var el = element.createElement;
  var InnerBlocks = blockEditor.InnerBlocks;
  var useBlockProps = blockEditor.useBlockProps;
  blocks.registerBlockType('blythe/lyrics-section', {
    title: 'Lyrics Section',
    category: 'design',
    edit: function edit() {
      var blockProps = useBlockProps();
      return el('div', blockProps, el(InnerBlocks));
    },
    save: function save() {
      var blockProps = useBlockProps.save();
      return el('div', blockProps, el(InnerBlocks.Content));
    }
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor);

/***/ }),

/***/ "./assets/src/scripts/admin/blythefamily-admin.js":
/*!********************************************************!*\
  !*** ./assets/src/scripts/admin/blythefamily-admin.js ***!
  \********************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _admin_blocks_lyrics_section__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../admin/blocks/_lyrics-section */ "./assets/src/scripts/admin/blocks/_lyrics-section.js");
/* harmony import */ var _admin_blocks_lyrics_section__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_admin_blocks_lyrics_section__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _admin_blocks_lyrics_section_label__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../admin/blocks/_lyrics-section-label */ "./assets/src/scripts/admin/blocks/_lyrics-section-label.js");
/* harmony import */ var _admin_blocks_lyrics_section_label__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_admin_blocks_lyrics_section_label__WEBPACK_IMPORTED_MODULE_1__);



/***/ }),

/***/ 0:
/*!**************************************************************!*\
  !*** multi ./assets/src/scripts/admin/blythefamily-admin.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! S:\WORDPRESS\blythefamily\wp-content\plugins\blythefamily\assets\src\scripts\admin\blythefamily-admin.js */"./assets/src/scripts/admin/blythefamily-admin.js");


/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3NyYy9zY3JpcHRzL2FkbWluL2Jsb2Nrcy9fbHlyaWNzLXNlY3Rpb24tbGFiZWwuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3NyYy9zY3JpcHRzL2FkbWluL2Jsb2Nrcy9fbHlyaWNzLXNlY3Rpb24uanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3NyYy9zY3JpcHRzL2FkbWluL2JseXRoZWZhbWlseS1hZG1pbi5qcyJdLCJuYW1lcyI6WyJibG9ja3MiLCJibG9ja0VkaXRvciIsImVsZW1lbnQiLCJlbCIsImNyZWF0ZUVsZW1lbnQiLCJSaWNoVGV4dCIsInVzZUJsb2NrUHJvcHMiLCJjcmVhdGVCbG9jayIsInJlZ2lzdGVyQmxvY2tUeXBlIiwiYXBpVmVyc2lvbiIsInRpdGxlIiwiaWNvbiIsImNhdGVnb3J5IiwiYXR0cmlidXRlcyIsImNvbnRlbnQiLCJ0eXBlIiwic291cmNlIiwic2VsZWN0b3IiLCJ0cmFuc2Zvcm1zIiwiZnJvbSIsInRyYW5zZm9ybSIsImxpbmVzIiwic3BsaXQiLCJsZW5ndGgiLCJuZXdCbG9ja3MiLCJpIiwicHVzaCIsInRvIiwiZXhhbXBsZSIsImVkaXQiLCJwcm9wcyIsImJsb2NrUHJvcHMiLCJvbkNoYW5nZUNvbnRlbnQiLCJuZXdDb250ZW50Iiwic2V0QXR0cmlidXRlcyIsIm9uU3BsaXRDb250ZW50IiwidmFsdWUiLCJpc09yaWdpbmFsIiwiYmxvY2siLCJjbGllbnRJZCIsIk9iamVjdCIsImFzc2lnbiIsInRhZ05hbWUiLCJvbkNoYW5nZSIsInBsYWNlaG9sZGVyIiwia2VlcFBsYWNlaG9sZGVyT25Gb2N1cyIsIm11bHRpbGluZSIsIm9uU3BsaXQiLCJvblJlcGxhY2UiLCJvblJlbW92ZSIsInVuZGVmaW5lZCIsInNhdmUiLCJDb250ZW50IiwiY2xhc3NOYW1lIiwid2luZG93Iiwid3AiLCJJbm5lckJsb2NrcyJdLCJtYXBwaW5ncyI6IjtRQUFBO1FBQ0E7O1FBRUE7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTs7UUFFQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBOzs7UUFHQTtRQUNBOztRQUVBO1FBQ0E7O1FBRUE7UUFDQTtRQUNBO1FBQ0EsMENBQTBDLGdDQUFnQztRQUMxRTtRQUNBOztRQUVBO1FBQ0E7UUFDQTtRQUNBLHdEQUF3RCxrQkFBa0I7UUFDMUU7UUFDQSxpREFBaUQsY0FBYztRQUMvRDs7UUFFQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0E7UUFDQTtRQUNBO1FBQ0EseUNBQXlDLGlDQUFpQztRQUMxRSxnSEFBZ0gsbUJBQW1CLEVBQUU7UUFDckk7UUFDQTs7UUFFQTtRQUNBO1FBQ0E7UUFDQSwyQkFBMkIsMEJBQTBCLEVBQUU7UUFDdkQsaUNBQWlDLGVBQWU7UUFDaEQ7UUFDQTtRQUNBOztRQUVBO1FBQ0Esc0RBQXNELCtEQUErRDs7UUFFckg7UUFDQTs7O1FBR0E7UUFDQTs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDbEZBLENBQUUsVUFBV0EsTUFBWCxFQUFtQkMsV0FBbkIsRUFBZ0NDLE9BQWhDLEVBQTBDO0FBQ3hDLE1BQUlDLEVBQUUsR0FBR0QsT0FBTyxDQUFDRSxhQUFqQjtBQUNBLE1BQUlDLFFBQVEsR0FBR0osV0FBVyxDQUFDSSxRQUEzQjtBQUNBLE1BQUlDLGFBQWEsR0FBR0wsV0FBVyxDQUFDSyxhQUFoQztBQUNBLE1BQUlDLFdBQVcsR0FBR1AsTUFBTSxDQUFDTyxXQUF6QjtBQUVBUCxRQUFNLENBQUNRLGlCQUFQLENBQTBCLDZCQUExQixFQUF5RDtBQUNyREMsY0FBVSxFQUFFLENBRHlDO0FBRXJEQyxTQUFLLEVBQUUsc0JBRjhDO0FBR3JEQyxRQUFJLEVBQUUsWUFIK0M7QUFJckRDLFlBQVEsRUFBRSxRQUoyQztBQU1yREMsY0FBVSxFQUFFO0FBQ1JDLGFBQU8sRUFBRTtBQUNMQyxZQUFJLEVBQUUsT0FERDtBQUVMQyxjQUFNLEVBQUUsVUFGSDtBQUdMQyxnQkFBUSxFQUFFO0FBSEw7QUFERCxLQU55QztBQWFyREMsY0FBVSxFQUFFO0FBQ1JDLFVBQUksRUFBRSxDQUNGO0FBQ0lKLFlBQUksRUFBRSxPQURWO0FBRUlmLGNBQU0sRUFBRSxDQUFFLGdCQUFGLEVBQW9CLHVCQUFwQixDQUZaO0FBR0lvQixpQkFBUyxFQUFFLG1CQUFXUCxVQUFYLEVBQXdCO0FBRS9CLGNBQUlRLEtBQUssR0FBSyxPQUFPUixVQUFVLENBQUNDLE9BQXBCLEtBQWtDLFFBQWxDLEdBQTZDRCxVQUFVLENBQUNDLE9BQVgsQ0FBbUJRLEtBQW5CLENBQXlCLE1BQXpCLENBQTdDLEdBQWdGLEVBQTVGOztBQUVBLGNBQUtELEtBQUssQ0FBQ0UsTUFBTixHQUFlLENBQXBCLEVBQXdCO0FBQ3BCLGdCQUFJQyxTQUFTLEdBQUcsRUFBaEI7O0FBRUEsaUJBQU0sSUFBSUMsQ0FBQyxHQUFHLENBQWQsRUFBaUJBLENBQUMsR0FBR0osS0FBSyxDQUFDRSxNQUEzQixFQUFtQ0UsQ0FBQyxFQUFwQyxFQUF5QztBQUNyQ0QsdUJBQVMsQ0FBQ0UsSUFBVixDQUFnQm5CLFdBQVcsQ0FBR2tCLENBQUMsSUFBSSxDQUFMLEdBQVMsNkJBQVQsR0FBeUMsdUJBQTVDLEVBQXFFO0FBQzVGWCx1QkFBTyxFQUFFTyxLQUFLLENBQUNJLENBQUQ7QUFEOEUsZUFBckUsQ0FBM0I7QUFHSDs7QUFDRCxtQkFBT0QsU0FBUDtBQUNILFdBYjhCLENBYy9COzs7QUFFQSxpQkFBT2pCLFdBQVcsQ0FBRSx1QkFBRixFQUEyQjtBQUFFTyxtQkFBTyxFQUFFRCxVQUFVLENBQUNDO0FBQXRCLFdBQTNCLENBQWxCO0FBRUg7QUFyQkwsT0FERSxDQURFO0FBMEJSYSxRQUFFLEVBQUUsQ0FDQTtBQUNJWixZQUFJLEVBQUUsT0FEVjtBQUVJZixjQUFNLEVBQUUsQ0FBRSx1QkFBRixDQUZaO0FBR0lvQixpQkFBUyxFQUFFLG1CQUFXUCxVQUFYLEVBQXdCO0FBRS9CLGlCQUFPTixXQUFXLENBQUUsdUJBQUYsRUFBMkI7QUFDekNPLG1CQUFPLEVBQUVELFVBQVUsQ0FBQ0M7QUFEcUIsV0FBM0IsQ0FBbEI7QUFHSDtBQVJMLE9BREE7QUExQkksS0FieUM7QUFvRHJEYyxXQUFPLEVBQUU7QUFDTGYsZ0JBQVUsRUFBRTtBQUNSQyxlQUFPLEVBQUU7QUFERDtBQURQLEtBcEQ0QztBQXlEckRlLFFBQUksRUFBRSxjQUFXQyxLQUFYLEVBQW1CO0FBQ3JCLFVBQUlDLFVBQVUsR0FBR3pCLGFBQWEsRUFBOUI7QUFDQSxVQUFJUSxPQUFPLEdBQUdnQixLQUFLLENBQUNqQixVQUFOLENBQWlCQyxPQUEvQjs7QUFDQSxlQUFTa0IsZUFBVCxDQUEwQkMsVUFBMUIsRUFBdUM7QUFDbkNILGFBQUssQ0FBQ0ksYUFBTixDQUFxQjtBQUFFcEIsaUJBQU8sRUFBRW1CO0FBQVgsU0FBckI7QUFDSDs7QUFFRCxlQUFTRSxjQUFULENBQXlCQyxLQUF6QixFQUFnQ0MsVUFBaEMsRUFBNkM7QUFFekMsWUFBSUMsS0FBSjtBQUVBQSxhQUFLLEdBQUcvQixXQUFXLENBQUU4QixVQUFVLEdBQUcsNkJBQUgsR0FBbUMsdUJBQS9DLGtDQUNaUCxLQUFLLENBQUNqQixVQURNO0FBRWZDLGlCQUFPLEVBQUVzQjtBQUZNLFdBQW5COztBQU1BLFlBQUtDLFVBQUwsRUFBa0I7QUFDZEMsZUFBSyxDQUFDQyxRQUFOLEdBQWlCQSxRQUFqQjtBQUNIOztBQUVELGVBQU9ELEtBQVA7QUFDSDs7QUFFRCxhQUFPbkMsRUFBRSxDQUNMRSxRQURLLEVBRUxtQyxNQUFNLENBQUNDLE1BQVAsQ0FBZVYsVUFBZixFQUEyQjtBQUN2QlcsZUFBTyxFQUFFLE1BRGM7QUFFdkJDLGdCQUFRLEVBQUVYLGVBRmE7QUFHdkJZLG1CQUFXLEVBQUUseUJBSFU7QUFJdkJDLDhCQUFzQixFQUFFLElBSkQ7QUFLdkJDLGlCQUFTLEVBQUUsS0FMWTtBQU12QkMsZUFBTyxFQUFFWixjQU5jO0FBT3ZCYSxpQkFBUyxFQUFFbEIsS0FBSyxDQUFDa0IsU0FQTTtBQVF2QkMsZ0JBQVEsRUFBRW5CLEtBQUssQ0FBQ2tCLFNBQU4sR0FBa0I7QUFBQSxpQkFBTWxCLEtBQUssQ0FBQ2tCLFNBQU4sQ0FBaUIsRUFBakIsQ0FBTjtBQUFBLFNBQWxCLEdBQWdERSxTQVJuQztBQVN2QmQsYUFBSyxFQUFFdEI7QUFUZ0IsT0FBM0IsQ0FGSyxDQUFUO0FBY0gsS0EvRm9EO0FBaUdyRHFDLFFBQUksRUFBRSxjQUFXckIsS0FBWCxFQUFtQjtBQUNyQixVQUFJQyxVQUFVLEdBQUd6QixhQUFhLENBQUM2QyxJQUFkLEVBQWpCO0FBQ0EsYUFBT2hELEVBQUUsQ0FDTEUsUUFBUSxDQUFDK0MsT0FESixFQUVMWixNQUFNLENBQUNDLE1BQVAsQ0FBZVYsVUFBZixFQUEyQjtBQUN2QlcsZUFBTyxFQUFFLE1BRGM7QUFFdkJXLGlCQUFTLEVBQUUsc0JBRlk7QUFHdkJqQixhQUFLLEVBQUVOLEtBQUssQ0FBQ2pCLFVBQU4sQ0FBaUJDO0FBSEQsT0FBM0IsQ0FGSyxDQUFUO0FBUUg7QUEzR29ELEdBQXpEO0FBNkdILENBbkhELEVBbUhLd0MsTUFBTSxDQUFDQyxFQUFQLENBQVV2RCxNQW5IZixFQW1IdUJzRCxNQUFNLENBQUNDLEVBQVAsQ0FBVXRELFdBbkhqQyxFQW1IOENxRCxNQUFNLENBQUNDLEVBQVAsQ0FBVXJELE9Bbkh4RCxFOzs7Ozs7Ozs7OztBQ0FBLENBQUUsVUFBV0YsTUFBWCxFQUFtQkUsT0FBbkIsRUFBNEJELFdBQTVCLEVBQTBDO0FBQ3hDLE1BQUlFLEVBQUUsR0FBR0QsT0FBTyxDQUFDRSxhQUFqQjtBQUNBLE1BQUlvRCxXQUFXLEdBQUd2RCxXQUFXLENBQUN1RCxXQUE5QjtBQUNBLE1BQUlsRCxhQUFhLEdBQUdMLFdBQVcsQ0FBQ0ssYUFBaEM7QUFFQU4sUUFBTSxDQUFDUSxpQkFBUCxDQUEwQix1QkFBMUIsRUFBbUQ7QUFDL0NFLFNBQUssRUFBRSxnQkFEd0M7QUFFL0NFLFlBQVEsRUFBRSxRQUZxQztBQUkvQ2lCLFFBQUksRUFBRSxnQkFBWTtBQUNkLFVBQUlFLFVBQVUsR0FBR3pCLGFBQWEsRUFBOUI7QUFFQSxhQUFPSCxFQUFFLENBQUUsS0FBRixFQUFTNEIsVUFBVCxFQUFxQjVCLEVBQUUsQ0FBRXFELFdBQUYsQ0FBdkIsQ0FBVDtBQUNILEtBUjhDO0FBVS9DTCxRQUFJLEVBQUUsZ0JBQVk7QUFDZCxVQUFJcEIsVUFBVSxHQUFHekIsYUFBYSxDQUFDNkMsSUFBZCxFQUFqQjtBQUVBLGFBQU9oRCxFQUFFLENBQUUsS0FBRixFQUFTNEIsVUFBVCxFQUFxQjVCLEVBQUUsQ0FBRXFELFdBQVcsQ0FBQ0osT0FBZCxDQUF2QixDQUFUO0FBQ0g7QUFkOEMsR0FBbkQ7QUFnQkgsQ0FyQkQsRUFxQktFLE1BQU0sQ0FBQ0MsRUFBUCxDQUFVdkQsTUFyQmYsRUFxQnVCc0QsTUFBTSxDQUFDQyxFQUFQLENBQVVyRCxPQXJCakMsRUFxQjBDb0QsTUFBTSxDQUFDQyxFQUFQLENBQVV0RCxXQXJCcEQsRTs7Ozs7Ozs7Ozs7O0FDQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBIiwiZmlsZSI6ImJseXRoZWZhbWlseS1hZG1pbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGdldHRlciB9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuIFx0XHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcbiBcdFx0fVxuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xuIFx0fTtcblxuIFx0Ly8gY3JlYXRlIGEgZmFrZSBuYW1lc3BhY2Ugb2JqZWN0XG4gXHQvLyBtb2RlICYgMTogdmFsdWUgaXMgYSBtb2R1bGUgaWQsIHJlcXVpcmUgaXRcbiBcdC8vIG1vZGUgJiAyOiBtZXJnZSBhbGwgcHJvcGVydGllcyBvZiB2YWx1ZSBpbnRvIHRoZSBuc1xuIFx0Ly8gbW9kZSAmIDQ6IHJldHVybiB2YWx1ZSB3aGVuIGFscmVhZHkgbnMgb2JqZWN0XG4gXHQvLyBtb2RlICYgOHwxOiBiZWhhdmUgbGlrZSByZXF1aXJlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnQgPSBmdW5jdGlvbih2YWx1ZSwgbW9kZSkge1xuIFx0XHRpZihtb2RlICYgMSkgdmFsdWUgPSBfX3dlYnBhY2tfcmVxdWlyZV9fKHZhbHVlKTtcbiBcdFx0aWYobW9kZSAmIDgpIHJldHVybiB2YWx1ZTtcbiBcdFx0aWYoKG1vZGUgJiA0KSAmJiB0eXBlb2YgdmFsdWUgPT09ICdvYmplY3QnICYmIHZhbHVlICYmIHZhbHVlLl9fZXNNb2R1bGUpIHJldHVybiB2YWx1ZTtcbiBcdFx0dmFyIG5zID0gT2JqZWN0LmNyZWF0ZShudWxsKTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yKG5zKTtcbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KG5zLCAnZGVmYXVsdCcsIHsgZW51bWVyYWJsZTogdHJ1ZSwgdmFsdWU6IHZhbHVlIH0pO1xuIFx0XHRpZihtb2RlICYgMiAmJiB0eXBlb2YgdmFsdWUgIT0gJ3N0cmluZycpIGZvcih2YXIga2V5IGluIHZhbHVlKSBfX3dlYnBhY2tfcmVxdWlyZV9fLmQobnMsIGtleSwgZnVuY3Rpb24oa2V5KSB7IHJldHVybiB2YWx1ZVtrZXldOyB9LmJpbmQobnVsbCwga2V5KSk7XG4gXHRcdHJldHVybiBucztcbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSAwKTtcbiIsIiggZnVuY3Rpb24gKCBibG9ja3MsIGJsb2NrRWRpdG9yLCBlbGVtZW50ICkge1xyXG4gICAgdmFyIGVsID0gZWxlbWVudC5jcmVhdGVFbGVtZW50O1xyXG4gICAgdmFyIFJpY2hUZXh0ID0gYmxvY2tFZGl0b3IuUmljaFRleHQ7XHJcbiAgICB2YXIgdXNlQmxvY2tQcm9wcyA9IGJsb2NrRWRpdG9yLnVzZUJsb2NrUHJvcHM7XHJcbiAgICB2YXIgY3JlYXRlQmxvY2sgPSBibG9ja3MuY3JlYXRlQmxvY2s7XHJcblxyXG4gICAgYmxvY2tzLnJlZ2lzdGVyQmxvY2tUeXBlKCAnYmx5dGhlL2x5cmljcy1zZWN0aW9uLWxhYmVsJywge1xyXG4gICAgICAgIGFwaVZlcnNpb246IDIsXHJcbiAgICAgICAgdGl0bGU6ICdMeXJpY3MgU2VjdGlvbiBMYWJlbCcsXHJcbiAgICAgICAgaWNvbjogJ2VkaXRvci1sdHInLFxyXG4gICAgICAgIGNhdGVnb3J5OiAnZGVzaWduJyxcclxuXHJcbiAgICAgICAgYXR0cmlidXRlczoge1xyXG4gICAgICAgICAgICBjb250ZW50OiB7XHJcbiAgICAgICAgICAgICAgICB0eXBlOiAnYXJyYXknLFxyXG4gICAgICAgICAgICAgICAgc291cmNlOiAnY2hpbGRyZW4nLFxyXG4gICAgICAgICAgICAgICAgc2VsZWN0b3I6ICdzcGFuJyxcclxuICAgICAgICAgICAgfSxcclxuICAgICAgICB9LFxyXG4gICAgICAgIHRyYW5zZm9ybXM6IHtcclxuICAgICAgICAgICAgZnJvbTogW1xyXG4gICAgICAgICAgICAgICAge1xyXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdibG9jaycsXHJcbiAgICAgICAgICAgICAgICAgICAgYmxvY2tzOiBbICdjb3JlL3BhcmFncmFwaCcsICdibHl0aGUvbHlyaWNzLXNlY3Rpb24nIF0sXHJcbiAgICAgICAgICAgICAgICAgICAgdHJhbnNmb3JtOiBmdW5jdGlvbiAoIGF0dHJpYnV0ZXMgKSB7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgbGluZXMgPSAoIHR5cGVvZiBhdHRyaWJ1dGVzLmNvbnRlbnQgKSA9PT0gJ3N0cmluZycgPyBhdHRyaWJ1dGVzLmNvbnRlbnQuc3BsaXQoJzxicj4nKSA6ICcnO1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCBsaW5lcy5sZW5ndGggPiAxICkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG5ld0Jsb2NrcyA9IFtdO1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZvciAoIHZhciBpID0gMDsgaSA8IGxpbmVzLmxlbmd0aDsgaSsrICkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5ld0Jsb2Nrcy5wdXNoKCBjcmVhdGVCbG9jayggIGkgPT0gMCA/ICdibHl0aGUvbHlyaWNzLXNlY3Rpb24tbGFiZWwnIDogJ2JseXRoZS9seXJpY3Mtc2VjdGlvbicsIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY29udGVudDogbGluZXNbaV0sXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSApICk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gbmV3QmxvY2tzO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vZWxzZVxyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGNyZWF0ZUJsb2NrKCAnYmx5dGhlL2x5cmljcy1zZWN0aW9uJywgeyBjb250ZW50OiBhdHRyaWJ1dGVzLmNvbnRlbnQgfSk7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICBdLFxyXG4gICAgICAgICAgICB0bzogW1xyXG4gICAgICAgICAgICAgICAge1xyXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdibG9jaycsXHJcbiAgICAgICAgICAgICAgICAgICAgYmxvY2tzOiBbICdibHl0aGUvbHlyaWNzLXNlY3Rpb24nIF0sXHJcbiAgICAgICAgICAgICAgICAgICAgdHJhbnNmb3JtOiBmdW5jdGlvbiAoIGF0dHJpYnV0ZXMgKSB7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gY3JlYXRlQmxvY2soICdibHl0aGUvbHlyaWNzLXNlY3Rpb24nLCB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb250ZW50OiBhdHRyaWJ1dGVzLmNvbnRlbnQsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gKSA7XHJcbiAgICAgICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIF0sXHJcbiAgICAgICAgfSxcclxuICAgICAgICBleGFtcGxlOiB7XHJcbiAgICAgICAgICAgIGF0dHJpYnV0ZXM6IHtcclxuICAgICAgICAgICAgICAgIGNvbnRlbnQ6ICdIZWxsbyBXb3JsZCcsXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgfSxcclxuICAgICAgICBlZGl0OiBmdW5jdGlvbiAoIHByb3BzICkge1xyXG4gICAgICAgICAgICB2YXIgYmxvY2tQcm9wcyA9IHVzZUJsb2NrUHJvcHMoKTtcclxuICAgICAgICAgICAgdmFyIGNvbnRlbnQgPSBwcm9wcy5hdHRyaWJ1dGVzLmNvbnRlbnQ7XHJcbiAgICAgICAgICAgIGZ1bmN0aW9uIG9uQ2hhbmdlQ29udGVudCggbmV3Q29udGVudCApIHtcclxuICAgICAgICAgICAgICAgIHByb3BzLnNldEF0dHJpYnV0ZXMoIHsgY29udGVudDogbmV3Q29udGVudCB9ICk7XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIGZ1bmN0aW9uIG9uU3BsaXRDb250ZW50KCB2YWx1ZSwgaXNPcmlnaW5hbCApIHtcclxuXHJcbiAgICAgICAgICAgICAgICBsZXQgYmxvY2s7XHJcblxyXG4gICAgICAgICAgICAgICAgYmxvY2sgPSBjcmVhdGVCbG9jayggaXNPcmlnaW5hbCA/ICdibHl0aGUvbHlyaWNzLXNlY3Rpb24tbGFiZWwnIDogJ2JseXRoZS9seXJpY3Mtc2VjdGlvbicsIHtcclxuICAgICAgICAgICAgICAgICAgICAuLi5wcm9wcy5hdHRyaWJ1dGVzLFxyXG4gICAgICAgICAgICAgICAgICAgIGNvbnRlbnQ6IHZhbHVlLFxyXG4gICAgICAgICAgICAgICAgfSApO1xyXG5cclxuXHJcbiAgICAgICAgICAgICAgICBpZiAoIGlzT3JpZ2luYWwgKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgYmxvY2suY2xpZW50SWQgPSBjbGllbnRJZDtcclxuICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICByZXR1cm4gYmxvY2s7XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgIHJldHVybiBlbChcclxuICAgICAgICAgICAgICAgIFJpY2hUZXh0LFxyXG4gICAgICAgICAgICAgICAgT2JqZWN0LmFzc2lnbiggYmxvY2tQcm9wcywge1xyXG4gICAgICAgICAgICAgICAgICAgIHRhZ05hbWU6ICdzcGFuJyxcclxuICAgICAgICAgICAgICAgICAgICBvbkNoYW5nZTogb25DaGFuZ2VDb250ZW50LFxyXG4gICAgICAgICAgICAgICAgICAgIHBsYWNlaG9sZGVyOiAnTHlyaWNzIHNlY3Rpb24gbGFiZWwuLi4nLFxyXG4gICAgICAgICAgICAgICAgICAgIGtlZXBQbGFjZWhvbGRlck9uRm9jdXM6IHRydWUsXHJcbiAgICAgICAgICAgICAgICAgICAgbXVsdGlsaW5lOiBmYWxzZSxcclxuICAgICAgICAgICAgICAgICAgICBvblNwbGl0OiBvblNwbGl0Q29udGVudCxcclxuICAgICAgICAgICAgICAgICAgICBvblJlcGxhY2U6IHByb3BzLm9uUmVwbGFjZSxcclxuICAgICAgICAgICAgICAgICAgICBvblJlbW92ZTogcHJvcHMub25SZXBsYWNlID8gKCkgPT4gcHJvcHMub25SZXBsYWNlKCBbXSApIDogdW5kZWZpbmVkLFxyXG4gICAgICAgICAgICAgICAgICAgIHZhbHVlOiBjb250ZW50LFxyXG4gICAgICAgICAgICAgICAgfSApXHJcbiAgICAgICAgICAgICk7XHJcbiAgICAgICAgfSxcclxuXHJcbiAgICAgICAgc2F2ZTogZnVuY3Rpb24gKCBwcm9wcyApIHtcclxuICAgICAgICAgICAgdmFyIGJsb2NrUHJvcHMgPSB1c2VCbG9ja1Byb3BzLnNhdmUoKTtcclxuICAgICAgICAgICAgcmV0dXJuIGVsKFxyXG4gICAgICAgICAgICAgICAgUmljaFRleHQuQ29udGVudCxcclxuICAgICAgICAgICAgICAgIE9iamVjdC5hc3NpZ24oIGJsb2NrUHJvcHMsIHtcclxuICAgICAgICAgICAgICAgICAgICB0YWdOYW1lOiAnc3BhbicsXHJcbiAgICAgICAgICAgICAgICAgICAgY2xhc3NOYW1lOiAnbHlyaWNzLXNlY3Rpb24tbGFiZWwnLFxyXG4gICAgICAgICAgICAgICAgICAgIHZhbHVlOiBwcm9wcy5hdHRyaWJ1dGVzLmNvbnRlbnQsXHJcbiAgICAgICAgICAgICAgICB9IClcclxuICAgICAgICAgICAgKTtcclxuICAgICAgICB9LFxyXG4gICAgfSApO1xyXG59ICkoIHdpbmRvdy53cC5ibG9ja3MsIHdpbmRvdy53cC5ibG9ja0VkaXRvciwgd2luZG93LndwLmVsZW1lbnQgKTsiLCIoIGZ1bmN0aW9uICggYmxvY2tzLCBlbGVtZW50LCBibG9ja0VkaXRvciApIHtcclxuICAgIHZhciBlbCA9IGVsZW1lbnQuY3JlYXRlRWxlbWVudDtcclxuICAgIHZhciBJbm5lckJsb2NrcyA9IGJsb2NrRWRpdG9yLklubmVyQmxvY2tzO1xyXG4gICAgdmFyIHVzZUJsb2NrUHJvcHMgPSBibG9ja0VkaXRvci51c2VCbG9ja1Byb3BzO1xyXG5cclxuICAgIGJsb2Nrcy5yZWdpc3RlckJsb2NrVHlwZSggJ2JseXRoZS9seXJpY3Mtc2VjdGlvbicsIHtcclxuICAgICAgICB0aXRsZTogJ0x5cmljcyBTZWN0aW9uJyxcclxuICAgICAgICBjYXRlZ29yeTogJ2Rlc2lnbicsXHJcblxyXG4gICAgICAgIGVkaXQ6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgdmFyIGJsb2NrUHJvcHMgPSB1c2VCbG9ja1Byb3BzKCk7XHJcblxyXG4gICAgICAgICAgICByZXR1cm4gZWwoICdkaXYnLCBibG9ja1Byb3BzLCBlbCggSW5uZXJCbG9ja3MgKSApO1xyXG4gICAgICAgIH0sXHJcblxyXG4gICAgICAgIHNhdmU6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgdmFyIGJsb2NrUHJvcHMgPSB1c2VCbG9ja1Byb3BzLnNhdmUoKTtcclxuXHJcbiAgICAgICAgICAgIHJldHVybiBlbCggJ2RpdicsIGJsb2NrUHJvcHMsIGVsKCBJbm5lckJsb2Nrcy5Db250ZW50ICkgKTtcclxuICAgICAgICB9LFxyXG4gICAgfSApO1xyXG59ICkoIHdpbmRvdy53cC5ibG9ja3MsIHdpbmRvdy53cC5lbGVtZW50LCB3aW5kb3cud3AuYmxvY2tFZGl0b3IgKTsiLCJpbXBvcnQgJy4uL2FkbWluL2Jsb2Nrcy9fbHlyaWNzLXNlY3Rpb24nO1xyXG5pbXBvcnQgJy4uL2FkbWluL2Jsb2Nrcy9fbHlyaWNzLXNlY3Rpb24tbGFiZWwnOyJdLCJzb3VyY2VSb290IjoiIn0=