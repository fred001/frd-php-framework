//example 2: is_int('23')
//returns 2: false
function is_int(mixed_var) {
  return mixed_var === +mixed_var && isFinite(mixed_var) && !(mixed_var % 1);
}

function to_str(mixed_var){
 return mixed_var+"";
}
function strpos(haystack, needle, offset) {
  var i = (haystack + '')
    .indexOf(needle, (offset || 0));
  return i === -1 ? false : i;
}

function substr(str, start, len) {
  //   example 3: substr('a\uD801\uDC00', 0, -1);
  //   returns 3: 'a'
  var i = 0,
    allBMP = true,
    es = 0,
    el = 0,
    se = 0,
    ret = '';
  str += '';
  var end = str.length;

  // BEGIN REDUNDANT
  this.php_js = this.php_js || {};
  this.php_js.ini = this.php_js.ini || {};
  // END REDUNDANT
  switch ((this.php_js.ini['unicode.semantics'] && this.php_js.ini['unicode.semantics'].local_value.toLowerCase())) {
    case 'on':
      // Full-blown Unicode including non-Basic-Multilingual-Plane characters
      // strlen()
      for (i = 0; i < str.length; i++) {
        if (/[\uD800-\uDBFF]/.test(str.charAt(i)) && /[\uDC00-\uDFFF]/.test(str.charAt(i + 1))) {
          allBMP = false;
          break;
        }
      }

      if (!allBMP) {
        if (start < 0) {
          for (i = end - 1, es = (start += end); i >= es; i--) {
            if (/[\uDC00-\uDFFF]/.test(str.charAt(i)) && /[\uD800-\uDBFF]/.test(str.charAt(i - 1))) {
              start--;
              es--;
            }
          }
        } else {
          var surrogatePairs = /[\uD800-\uDBFF][\uDC00-\uDFFF]/g;
          while ((surrogatePairs.exec(str)) != null) {
            var li = surrogatePairs.lastIndex;
            if (li - 2 < start) {
              start++;
            } else {
              break;
            }
          }
        }

        if (start >= end || start < 0) {
          return false;
        }
        if (len < 0) {
          for (i = end - 1, el = (end += len); i >= el; i--) {
            if (/[\uDC00-\uDFFF]/.test(str.charAt(i)) && /[\uD800-\uDBFF]/.test(str.charAt(i - 1))) {
              end--;
              el--;
            }
          }
          if (start > end) {
            return false;
          }
          return str.slice(start, end);
        } else {
          se = start + len;
          for (i = start; i < se; i++) {
            ret += str.charAt(i);
            if (/[\uD800-\uDBFF]/.test(str.charAt(i)) && /[\uDC00-\uDFFF]/.test(str.charAt(i + 1))) {
              se++; // Go one further, since one of the "characters" is part of a surrogate pair
            }
          }
          return ret;
        }
        break;
      }
      // Fall-through
    case 'off':
      // assumes there are no non-BMP characters;
      //    if there may be such characters, then it is best to turn it on (critical in true XHTML/XML)
    default:
      if (start < 0) {
        start += end;
      }
      end = typeof len === 'undefined' ? end : (len < 0 ? len + end : len + start);
      // PHP returns false if start does not fall within the string.
      // PHP returns false if the calculated end comes before the calculated start.
      // PHP returns an empty string if start and end are the same.
      // Otherwise, PHP returns the portion of the string from start to end.
      return start >= str.length || start < 0 || start > end ? !1 : str.slice(start, end);
  }
  return undefined; // Please Netbeans
}


function trim(str, charlist) {
  var whitespace, l = 0,
    i = 0;
  str += '';

  if (!charlist) {
    // default list
    whitespace =
      ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
  } else {
    // preg_quote custom list
    charlist += '';
    whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
  }

  l = str.length;
  for (i = 0; i < l; i++) {
    if (whitespace.indexOf(str.charAt(i)) === -1) {
      str = str.substring(i);
      break;
    }
  }

  l = str.length;
  for (i = l - 1; i >= 0; i--) {
    if (whitespace.indexOf(str.charAt(i)) === -1) {
      str = str.substring(0, i + 1);
      break;
    }
  }

  return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}

function ltrim(str, charlist) {


  charlist = !charlist ? ' \\s\u00A0' : (charlist + '')
    .replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
  var re = new RegExp('^[' + charlist + ']+', 'g');
  return (str + '')
    .replace(re, '');
}

function rtrim(str, charlist) {

  charlist = !charlist ? ' \\s\u00A0' : (charlist + '')
    .replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\\$1');
  var re = new RegExp('[' + charlist + ']+$', 'g');
  return (str + '')
    .replace(re, '');
}


function explode(delimiter, string, limit) {
  //  discuss at: http://phpjs.org/functions/explode/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //   example 1: explode(' ', 'Kevin van Zonneveld');
  //   returns 1: {0: 'Kevin', 1: 'van', 2: 'Zonneveld'}

  if (arguments.length < 2 || typeof delimiter === 'undefined' || typeof string === 'undefined') return null;
  if (delimiter === '' || delimiter === false || delimiter === null) return false;
  if (typeof delimiter === 'function' || typeof delimiter === 'object' || typeof string === 'function' || typeof string ===
    'object') {
    return {
      0: ''
    };
  }
  if (delimiter === true) delimiter = '1';

  // Here we go...
  delimiter += '';
  string += '';

  var s = string.split(delimiter);

  if (typeof limit === 'undefined') return s;

  // Support for limit
  if (limit === 0) limit = 1;

  // Positive limit
  if (limit > 0) {
    if (limit >= s.length) return s;
    return s.slice(0, limit - 1)
      .concat([s.slice(limit - 1)
        .join(delimiter)
      ]);
  }

  // Negative limit
  if (-limit >= s.length) return [];

  s.splice(s.length + limit);
  return s;
}
function implode(glue, pieces) {
  //  discuss at: http://phpjs.org/functions/implode/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Waldo Malqui Silva
  // improved by: Itsacon (http://www.itsacon.net/)
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //   example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
  //   returns 1: 'Kevin van Zonneveld'
  //   example 2: implode(' ', {first:'Kevin', last: 'van Zonneveld'});
  //   returns 2: 'Kevin van Zonneveld'

  var i = '',
    retVal = '',
    tGlue = '';
  if (arguments.length === 1) {
    pieces = glue;
    glue = '';
  }
  if (typeof pieces === 'object') {
    if (Object.prototype.toString.call(pieces) === '[object Array]') {
      return pieces.join(glue);
    }
    for (i in pieces) {
      retVal += tGlue + pieces[i];
      tGlue = glue;
    }
    return retVal;
  }
  return pieces;
}

function in_array(needle, haystack, argStrict) {
  //  discuss at: http://phpjs.org/functions/in_array/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: vlado houba
  // improved by: Jonas Sciangula Street (Joni2Back)
  //    input by: Billy
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //   example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
  //   returns 1: true
  //   example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
  //   returns 2: false
  //   example 3: in_array(1, ['1', '2', '3']);
  //   example 3: in_array(1, ['1', '2', '3'], false);
  //   returns 3: true
  //   returns 3: true
  //   example 4: in_array(1, ['1', '2', '3'], true);
  //   returns 4: false

  var key = '',
    strict = !! argStrict;

  //we prevent the double check (strict && arr[key] === ndl) || (!strict && arr[key] == ndl)
  //in just one for, in order to improve the performance 
  //deciding wich type of comparation will do before walk array
  if (strict) {
    for (key in haystack) {
      if (haystack[key] === needle) {
        return true;
      }
    }
  } else {
    for (key in haystack) {
      if (haystack[key] == needle) {
        return true;
      }
    }
  }

  return false;
}

function array_pop(inputArr) {
  //  discuss at: http://phpjs.org/functions/array_pop/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //    input by: Brett Zamir (http://brett-zamir.me)
  //    input by: Theriault
  // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //        note: While IE (and other browsers) support iterating an object's
  //        note: own properties in order, if one attempts to add back properties
  //        note: in IE, they may end up in their former position due to their position
  //        note: being retained. So use of this function with "associative arrays"
  //        note: (objects) may lead to unexpected behavior in an IE environment if
  //        note: you add back properties with the same keys that you removed
  //   example 1: array_pop([0,1,2]);
  //   returns 1: 2
  //   example 2: data = {firstName: 'Kevin', surName: 'van Zonneveld'};
  //   example 2: lastElem = array_pop(data);
  //   example 2: $result = data
  //   returns 2: {firstName: 'Kevin'}

  var key = '',
    lastKey = '';

  if (inputArr.hasOwnProperty('length')) {
    // Indexed
    if (!inputArr.length) {
      // Done popping, are we?
      return null;
    }
    return inputArr.pop();
  } else {
    // Associative
    for (key in inputArr) {
      if (inputArr.hasOwnProperty(key)) {
        lastKey = key;
      }
    }
    if (lastKey) {
      var tmp = inputArr[lastKey];
      delete(inputArr[lastKey]);
      return tmp;
    } else {
      return null;
    }
  }
}


function array_push(inputArr) {
  //  discuss at: http://phpjs.org/functions/array_push/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Brett Zamir (http://brett-zamir.me)
  //        note: Note also that IE retains information about property position even
  //        note: after being supposedly deleted, so if you delete properties and then
  //        note: add back properties with the same keys (including numeric) that had
  //        note: been deleted, the order will be as before; thus, this function is not
  //        note: really recommended with associative arrays (objects) in IE environments
  //   example 1: array_push(['kevin','van'], 'zonneveld');
  //   returns 1: 3

  var i = 0,
    pr = '',
    argv = arguments,
    argc = argv.length,
    allDigits = /^\d$/,
    size = 0,
    highestIdx = 0,
    len = 0;
  if (inputArr.hasOwnProperty('length')) {
    for (i = 1; i < argc; i++) {
      inputArr[inputArr.length] = argv[i];
    }
    return inputArr.length;
  }

  // Associative (object)
  for (pr in inputArr) {
    if (inputArr.hasOwnProperty(pr)) {
      ++len;
      if (pr.search(allDigits) !== -1) {
        size = parseInt(pr, 10);
        highestIdx = size > highestIdx ? size : highestIdx;
      }
    }
  }
  for (i = 1; i < argc; i++) {
    inputArr[++highestIdx] = argv[i];
  }
  return len + i - 1;
}
function array_shift(inputArr) {
  //  discuss at: http://phpjs.org/functions/array_shift/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Martijn Wieringa
  //        note: Currently does not handle objects
  //   example 1: array_shift(['Kevin', 'van', 'Zonneveld']);
  //   returns 1: 'Kevin'

  var props = false,
    shift = undefined,
    pr = '',
    allDigits = /^\d$/,
    int_ct = -1,
    _checkToUpIndices = function(arr, ct, key) {
      // Deal with situation, e.g., if encounter index 4 and try to set it to 0, but 0 exists later in loop (need to
      // increment all subsequent (skipping current key, since we need its value below) until find unused)
      if (arr[ct] !== undefined) {
        var tmp = ct;
        ct += 1;
        if (ct === key) {
          ct += 1;
        }
        ct = _checkToUpIndices(arr, ct, key);
        arr[ct] = arr[tmp];
        delete arr[tmp];
      }
      return ct;
    };

  if (inputArr.length === 0) {
    return null;
  }
  if (inputArr.length > 0) {
    return inputArr.shift();
  }

  /*
  UNFINISHED FOR HANDLING OBJECTS
  for (pr in inputArr) {
    if (inputArr.hasOwnProperty(pr)) {
      props = true;
      shift = inputArr[pr];
      delete inputArr[pr];
      break;
    }
  }
  for (pr in inputArr) {
    if (inputArr.hasOwnProperty(pr)) {
      if (pr.search(allDigits) !== -1) {
        int_ct += 1;
        if (parseInt(pr, 10) === int_ct) { // Key is already numbered ok, so don't need to change key for value
          continue;
        }
        _checkToUpIndices(inputArr, int_ct, pr);
        arr[int_ct] = arr[pr];
        delete arr[pr];
      }
    }
  }
  if (!props) {
    return null;
  }
  return shift;
  */
}


function empty(mixed_var) {
  //  discuss at: http://phpjs.org/functions/empty/
  // original by: Philippe Baumann
  //    input by: Onno Marsman
  //    input by: LH
  //    input by: Stoyan Kyosev (http://www.svest.org/)
  // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Onno Marsman
  // improved by: Francesco
  // improved by: Marc Jansen
  // improved by: Rafal Kukawski
  //   example 1: empty(null);
  //   returns 1: true
  //   example 2: empty(undefined);
  //   returns 2: true
  //   example 3: empty([]);
  //   returns 3: true
  //   example 4: empty({});
  //   returns 4: true
  //   example 5: empty({'aFunc' : function () { alert('humpty'); } });
  //   returns 5: false

  var undef, key, i, len;
  var emptyValues = [undef, null, false, 0, '', '0'];

  for (i = 0, len = emptyValues.length; i < len; i++) {
    if (mixed_var === emptyValues[i]) {
      return true;
    }
  }

  if (typeof mixed_var === 'object') {
    for (key in mixed_var) {
      // TODO: should we check for own properties only?
      //if (mixed_var.hasOwnProperty(key)) {
      return false;
      //}
    }
    return true;
  }

  return false;
}
function http_build_query(formdata, numeric_prefix, arg_separator) {
  //  discuss at: http://phpjs.org/functions/http_build_query/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Legaev Andrey
  // improved by: Michael White (http://getsprink.com)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Brett Zamir (http://brett-zamir.me)
  //  revised by: stag019
  //    input by: Dreamer
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  // bugfixed by: MIO_KODUKI (http://mio-koduki.blogspot.com/)
  //        note: If the value is null, key and value are skipped in the http_build_query of PHP while in phpjs they are not.
  //  depends on: urlencode
  //   example 1: http_build_query({foo: 'bar', php: 'hypertext processor', baz: 'boom', cow: 'milk'}, '', '&amp;');
  //   returns 1: 'foo=bar&amp;php=hypertext+processor&amp;baz=boom&amp;cow=milk'
  //   example 2: http_build_query({'php': 'hypertext processor', 0: 'foo', 1: 'bar', 2: 'baz', 3: 'boom', 'cow': 'milk'}, 'myvar_');
  //   returns 2: 'myvar_0=foo&myvar_1=bar&myvar_2=baz&myvar_3=boom&php=hypertext+processor&cow=milk'

  var value, key, tmp = [],
    that = this;

  var _http_build_query_helper = function(key, val, arg_separator) {
    var k, tmp = [];
    if (val === true) {
      val = '1';
    } else if (val === false) {
      val = '0';
    }
    if (val != null) {
      if (typeof val === 'object') {
        for (k in val) {
          if (val[k] != null) {
            tmp.push(_http_build_query_helper(key + '[' + k + ']', val[k], arg_separator));
          }
        }
        return tmp.join(arg_separator);
      } else if (typeof val !== 'function') {
        return that.urlencode(key) + '=' + that.urlencode(val);
      } else {
        throw new Error('There was an error processing for http_build_query().');
      }
    } else {
      return '';
    }
  };

  if (!arg_separator) {
    arg_separator = '&';
  }
  for (key in formdata) {
    value = formdata[key];
    if (numeric_prefix && !isNaN(key)) {
      key = String(numeric_prefix) + key;
    }
    var query = _http_build_query_helper(key, value, arg_separator);
    if (query !== '') {
      tmp.push(query);
    }
  }

  return tmp.join(arg_separator);
}
function urlencode(str) {
  //       discuss at: http://phpjs.org/functions/urlencode/
  //      original by: Philip Peterson
  //      improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //      improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //      improved by: Brett Zamir (http://brett-zamir.me)
  //      improved by: Lars Fischer
  //         input by: AJ
  //         input by: travc
  //         input by: Brett Zamir (http://brett-zamir.me)
  //         input by: Ratheous
  //      bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //      bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //      bugfixed by: Joris
  // reimplemented by: Brett Zamir (http://brett-zamir.me)
  // reimplemented by: Brett Zamir (http://brett-zamir.me)
  //             note: This reflects PHP 5.3/6.0+ behavior
  //             note: Please be aware that this function expects to encode into UTF-8 encoded strings, as found on
  //             note: pages served as UTF-8
  //        example 1: urlencode('Kevin van Zonneveld!');
  //        returns 1: 'Kevin+van+Zonneveld%21'
  //        example 2: urlencode('http://kevin.vanzonneveld.net/');
  //        returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
  //        example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
  //        returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'

  str = (str + '')
    .toString();

  // Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
  // PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
  return encodeURIComponent(str)
    .replace(/!/g, '%21')
    .replace(/'/g, '%27')
    .replace(/\(/g, '%28')
    .
  replace(/\)/g, '%29')
    .replace(/\*/g, '%2A')
    .replace(/%20/g, '+');
}
function urldecode(str) {
  //       discuss at: http://phpjs.org/functions/urldecode/
  //      original by: Philip Peterson
  //      improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //      improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //      improved by: Brett Zamir (http://brett-zamir.me)
  //      improved by: Lars Fischer
  //      improved by: Orlando
  //      improved by: Brett Zamir (http://brett-zamir.me)
  //      improved by: Brett Zamir (http://brett-zamir.me)
  //         input by: AJ
  //         input by: travc
  //         input by: Brett Zamir (http://brett-zamir.me)
  //         input by: Ratheous
  //         input by: e-mike
  //         input by: lovio
  //      bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //      bugfixed by: Rob
  // reimplemented by: Brett Zamir (http://brett-zamir.me)
  //             note: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
  //             note: Please be aware that this function expects to decode from UTF-8 encoded strings, as found on
  //             note: pages served as UTF-8
  //        example 1: urldecode('Kevin+van+Zonneveld%21');
  //        returns 1: 'Kevin van Zonneveld!'
  //        example 2: urldecode('http%3A%2F%2Fkevin.vanzonneveld.net%2F');
  //        returns 2: 'http://kevin.vanzonneveld.net/'
  //        example 3: urldecode('http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a');
  //        returns 3: 'http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'
  //        example 4: urldecode('%E5%A5%BD%3_4');
  //        returns 4: '\u597d%3_4'

  return decodeURIComponent((str + '')
    .replace(/%(?![\da-f]{2})/gi, function() {
      // PHP tolerates poorly formed escape sequences
      return '%25';
    })
    .replace(/\+/g, '%20'));
}


function isset() {
  var a = arguments,
      l = a.length,
      i = 0,
      undef;

  if (l === 0) {
    throw new Error('Empty isset');
  }

  while (i !== l) {
    if (a[i] === undef || a[i] === null) {
      return false;
    }
    i++;
  }
  return true;
}

function array_merge() {
  //  discuss at: http://phpjs.org/functions/array_merge/
  // original by: Brett Zamir (http://brett-zamir.me)
  // bugfixed by: Nate
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //    input by: josh
  //   example 1: arr1 = {"color": "red", 0: 2, 1: 4}
  //   example 1: arr2 = {0: "a", 1: "b", "color": "green", "shape": "trapezoid", 2: 4}
  //   example 1: array_merge(arr1, arr2)
  //   returns 1: {"color": "green", 0: 2, 1: 4, 2: "a", 3: "b", "shape": "trapezoid", 4: 4}
  //   example 2: arr1 = []
  //   example 2: arr2 = {1: "data"}
  //   example 2: array_merge(arr1, arr2)
  //   returns 2: {0: "data"}

  var args = Array.prototype.slice.call(arguments),
    argl = args.length,
    arg,
    retObj = {},
    k = '',
    argil = 0,
    j = 0,
    i = 0,
    ct = 0,
    toStr = Object.prototype.toString,
    retArr = true;

  for (i = 0; i < argl; i++) {
    if (toStr.call(args[i]) !== '[object Array]') {
      retArr = false;
      break;
    }
  }

  if (retArr) {
    retArr = [];
    for (i = 0; i < argl; i++) {
      retArr = retArr.concat(args[i]);
    }
    return retArr;
  }

  for (i = 0, ct = 0; i < argl; i++) {
    arg = args[i];
    if (toStr.call(arg) === '[object Array]') {
      for (j = 0, argil = arg.length; j < argil; j++) {
        retObj[ct++] = arg[j];
      }
    } else {
      for (k in arg) {
        if (arg.hasOwnProperty(k)) {
          if (parseInt(k, 10) + '' === k) {
            retObj[ct++] = arg[k];
          } else {
            retObj[k] = arg[k];
          }
        }
      }
    }
  }
  return retObj;
}

function addslashes(str) {
  //  discuss at: http://phpjs.org/functions/addslashes/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Ates Goral (http://magnetiq.com)
  // improved by: marrtins
  // improved by: Nate
  // improved by: Onno Marsman
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Oskar Larsson Högfeldt (http://oskar-lh.name/)
  //    input by: Denny Wardhana
  //   example 1: addslashes("kevin's birthday");
  //   returns 1: "kevin\\'s birthday"

  return (str + '')
    .replace(/[\\"']/g, '\\$&')
    .replace(/\u0000/g, '\\0');
}

