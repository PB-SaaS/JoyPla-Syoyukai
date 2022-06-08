/*
! tailwindcss v3.0.24 | MIT License | https://tailwindcss.com
*/

/*
1. Prevent padding and border from affecting element width. (https://github.com/mozdevs/cssremedy/issues/4)
2. Allow adding a border to an element by just adding a border-width. (https://github.com/tailwindcss/tailwindcss/pull/116)
*/

*,
::before,
::after {
  box-sizing: border-box;
  /* 1 */
  border-width: 0;
  /* 2 */
  border-style: solid;
  /* 2 */
  border-color: #e5e7eb;
  /* 2 */
}

::before,
::after {
  --tw-content: '';
}

/*
1. Use a consistent sensible line-height in all browsers.
2. Prevent adjustments of font size after orientation changes in iOS.
3. Use a more readable tab size.
4. Use the user's configured `sans` font-family by default.
*/

html {
  line-height: 1.5;
  /* 1 */
  -webkit-text-size-adjust: 100%;
  /* 2 */
  -moz-tab-size: 4;
  /* 3 */
  -o-tab-size: 4;
     tab-size: 4;
  /* 3 */
  font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
  /* 4 */
}

/*
1. Remove the margin in all browsers.
2. Inherit line-height from `html` so users can set them as a class directly on the `html` element.
*/

body {
  margin: 0;
  /* 1 */
  line-height: inherit;
  /* 2 */
}

/*
1. Add the correct height in Firefox.
2. Correct the inheritance of border color in Firefox. (https://bugzilla.mozilla.org/show_bug.cgi?id=190655)
3. Ensure horizontal rules are visible by default.
*/

hr {
  height: 0;
  /* 1 */
  color: inherit;
  /* 2 */
  border-top-width: 1px;
  /* 3 */
}

/*
Add the correct text decoration in Chrome, Edge, and Safari.
*/

abbr:where([title]) {
  -webkit-text-decoration: underline dotted;
          text-decoration: underline dotted;
}

/*
Remove the default font size and weight for headings.
*/

h1,
h2,
h3,
h4,
h5,
h6 {
  font-size: inherit;
  font-weight: inherit;
}

/*
Reset links to optimize for opt-in styling instead of opt-out.
*/

a {
  color: inherit;
  text-decoration: inherit;
}

/*
Add the correct font weight in Edge and Safari.
*/

b,
strong {
  font-weight: bolder;
}

/*
1. Use the user's configured `mono` font family by default.
2. Correct the odd `em` font sizing in all browsers.
*/

code,
kbd,
samp,
pre {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  /* 1 */
  font-size: 1em;
  /* 2 */
}

/*
Add the correct font size in all browsers.
*/

small {
  font-size: 80%;
}

/*
Prevent `sub` and `sup` elements from affecting the line height in all browsers.
*/

sub,
sup {
  font-size: 75%;
  line-height: 0;
  position: relative;
  vertical-align: baseline;
}

sub {
  bottom: -0.25em;
}

sup {
  top: -0.5em;
}

/*
1. Remove text indentation from table contents in Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=999088, https://bugs.webkit.org/show_bug.cgi?id=201297)
2. Correct table border color inheritance in all Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=935729, https://bugs.webkit.org/show_bug.cgi?id=195016)
3. Remove gaps between table borders by default.
*/

table {
  text-indent: 0;
  /* 1 */
  border-color: inherit;
  /* 2 */
  border-collapse: collapse;
  /* 3 */
}

/*
1. Change the font styles in all browsers.
2. Remove the margin in Firefox and Safari.
3. Remove default padding in all browsers.
*/

button,
input,
optgroup,
select,
textarea {
  font-family: inherit;
  /* 1 */
  font-size: 100%;
  /* 1 */
  line-height: inherit;
  /* 1 */
  color: inherit;
  /* 1 */
  margin: 0;
  /* 2 */
  padding: 0;
  /* 3 */
}

/*
Remove the inheritance of text transform in Edge and Firefox.
*/

button,
select {
  text-transform: none;
}

/*
1. Correct the inability to style clickable types in iOS and Safari.
2. Remove default button styles.
*/

button,
[type='button'],
[type='reset'],
[type='submit'] {
  -webkit-appearance: button;
  /* 1 */
  background-color: transparent;
  /* 2 */
  background-image: none;
  /* 2 */
}

/*
Use the modern Firefox focus style for all focusable elements.
*/

:-moz-focusring {
  outline: auto;
}

/*
Remove the additional `:invalid` styles in Firefox. (https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737)
*/

:-moz-ui-invalid {
  box-shadow: none;
}

/*
Add the correct vertical alignment in Chrome and Firefox.
*/

progress {
  vertical-align: baseline;
}

/*
Correct the cursor style of increment and decrement buttons in Safari.
*/

::-webkit-inner-spin-button,
::-webkit-outer-spin-button {
  height: auto;
}

/*
1. Correct the odd appearance in Chrome and Safari.
2. Correct the outline style in Safari.
*/

[type='search'] {
  -webkit-appearance: textfield;
  /* 1 */
  outline-offset: -2px;
  /* 2 */
}

/*
Remove the inner padding in Chrome and Safari on macOS.
*/

::-webkit-search-decoration {
  -webkit-appearance: none;
}

/*
1. Correct the inability to style clickable types in iOS and Safari.
2. Change font properties to `inherit` in Safari.
*/

::-webkit-file-upload-button {
  -webkit-appearance: button;
  /* 1 */
  font: inherit;
  /* 2 */
}

/*
Add the correct display in Chrome and Safari.
*/

summary {
  display: list-item;
}

/*
Removes the default spacing and border for appropriate elements.
*/

blockquote,
dl,
dd,
h1,
h2,
h3,
h4,
h5,
h6,
hr,
figure,
p,
pre {
  margin: 0;
}

fieldset {
  margin: 0;
  padding: 0;
}

legend {
  padding: 0;
}

ol,
ul,
menu {
  list-style: none;
  margin: 0;
  padding: 0;
}

/*
Prevent resizing textareas horizontally by default.
*/

textarea {
  resize: vertical;
}

/*
1. Reset the default placeholder opacity in Firefox. (https://github.com/tailwindlabs/tailwindcss/issues/3300)
2. Set the default placeholder color to the user's configured gray 400 color.
*/

input::-moz-placeholder, textarea::-moz-placeholder {
  opacity: 1;
  /* 1 */
  color: #9ca3af;
  /* 2 */
}

input:-ms-input-placeholder, textarea:-ms-input-placeholder {
  opacity: 1;
  /* 1 */
  color: #9ca3af;
  /* 2 */
}

input::placeholder,
textarea::placeholder {
  opacity: 1;
  /* 1 */
  color: #9ca3af;
  /* 2 */
}

/*
Set the default cursor for buttons.
*/

button,
[role="button"] {
  cursor: pointer;
}

/*
Make sure disabled buttons don't get the pointer cursor.
*/

:disabled {
  cursor: default;
}

/*
1. Make replaced elements `display: block` by default. (https://github.com/mozdevs/cssremedy/issues/14)
2. Add `vertical-align: middle` to align replaced elements more sensibly by default. (https://github.com/jensimmons/cssremedy/issues/14#issuecomment-634934210)
   This can trigger a poorly considered lint error in some tools but is included by design.
*/

img,
svg,
video,
canvas,
audio,
iframe,
embed,
object {
  display: block;
  /* 1 */
  vertical-align: middle;
  /* 2 */
}

/*
Constrain images and videos to the parent width and preserve their intrinsic aspect ratio. (https://github.com/mozdevs/cssremedy/issues/14)
*/

img,
video {
  max-width: 100%;
  height: auto;
}

/*
Ensure the default browser behavior of the `hidden` attribute.
*/

[hidden] {
  display: none;
}

*, ::before, ::after {
  --tw-translate-x: 0;
  --tw-translate-y: 0;
  --tw-rotate: 0;
  --tw-skew-x: 0;
  --tw-skew-y: 0;
  --tw-scale-x: 1;
  --tw-scale-y: 1;
  --tw-pan-x:  ;
  --tw-pan-y:  ;
  --tw-pinch-zoom:  ;
  --tw-scroll-snap-strictness: proximity;
  --tw-ordinal:  ;
  --tw-slashed-zero:  ;
  --tw-numeric-figure:  ;
  --tw-numeric-spacing:  ;
  --tw-numeric-fraction:  ;
  --tw-ring-inset:  ;
  --tw-ring-offset-width: 0px;
  --tw-ring-offset-color: #fff;
  --tw-ring-color: rgb(59 130 246 / 0.5);
  --tw-ring-offset-shadow: 0 0 #0000;
  --tw-ring-shadow: 0 0 #0000;
  --tw-shadow: 0 0 #0000;
  --tw-shadow-colored: 0 0 #0000;
  --tw-blur:  ;
  --tw-brightness:  ;
  --tw-contrast:  ;
  --tw-grayscale:  ;
  --tw-hue-rotate:  ;
  --tw-invert:  ;
  --tw-saturate:  ;
  --tw-sepia:  ;
  --tw-drop-shadow:  ;
  --tw-backdrop-blur:  ;
  --tw-backdrop-brightness:  ;
  --tw-backdrop-contrast:  ;
  --tw-backdrop-grayscale:  ;
  --tw-backdrop-hue-rotate:  ;
  --tw-backdrop-invert:  ;
  --tw-backdrop-opacity:  ;
  --tw-backdrop-saturate:  ;
  --tw-backdrop-sepia:  ;
}

.container {
  width: 100%;
}

@media (min-width: 640px) {
  .container {
    max-width: 640px;
  }
}

@media (min-width: 768px) {
  .container {
    max-width: 768px;
  }
}

@media (min-width: 1024px) {
  .container {
    max-width: 1024px;
  }
}

@media (min-width: 1280px) {
  .container {
    max-width: 1280px;
  }
}

@media (min-width: 1536px) {
  .container {
    max-width: 1536px;
  }
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}

.visible {
  visibility: visible;
}

.static {
  position: static;
}

.fixed {
  position: fixed;
}

.absolute {
  position: absolute;
}

.relative {
  position: relative;
}

.inset-0 {
  top: 0px;
  right: 0px;
  bottom: 0px;
  left: 0px;
}

.inset-y-0 {
  top: 0px;
  bottom: 0px;
}

.-right-2\.5 {
  right: -0.625rem;
}

.-top-2\.5 {
  top: -0.625rem;
}

.-right-2 {
  right: -0.5rem;
}

.-top-2 {
  top: -0.5rem;
}

.top-0 {
  top: 0px;
}

.left-0 {
  left: 0px;
}

.top-72 {
  top: 18rem;
}

.right-72 {
  right: 18rem;
}

.right-0 {
  right: 0px;
}

.-top-3 {
  top: -0.75rem;
}

.-top-1 {
  top: -0.25rem;
}

.-left-1 {
  left: -0.25rem;
}

.-left-2 {
  left: -0.5rem;
}

.-right-3 {
  right: -0.75rem;
}

.col-span-2 {
  grid-column: span 2 / span 2;
}

.float-left {
  float: left;
}

.mx-auto {
  margin-left: auto;
  margin-right: auto;
}

.my-10 {
  margin-top: 2.5rem;
  margin-bottom: 2.5rem;
}

.my-6 {
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
}

.my-2 {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
}

.mx-4 {
  margin-left: 1rem;
  margin-right: 1rem;
}

.mx-2 {
  margin-left: 0.5rem;
  margin-right: 0.5rem;
}

.mx-3 {
  margin-left: 0.75rem;
  margin-right: 0.75rem;
}

.my-3 {
  margin-top: 0.75rem;
  margin-bottom: 0.75rem;
}

.my-5 {
  margin-top: 1.25rem;
  margin-bottom: 1.25rem;
}

.mx-5 {
  margin-left: 1.25rem;
  margin-right: 1.25rem;
}

.mx-6 {
  margin-left: 1.5rem;
  margin-right: 1.5rem;
}

.mb-4 {
  margin-bottom: 1rem;
}

.mr-2 {
  margin-right: 0.5rem;
}

.mb-2 {
  margin-bottom: 0.5rem;
}

.mt-4 {
  margin-top: 1rem;
}

.ml-3 {
  margin-left: 0.75rem;
}

.ml-64 {
  margin-left: 16rem;
}

.ml-32 {
  margin-left: 8rem;
}

.mr-3 {
  margin-right: 0.75rem;
}

.ml-2 {
  margin-left: 0.5rem;
}

.mt-3 {
  margin-top: 0.75rem;
}

.mt-5 {
  margin-top: 1.25rem;
}

.mb-3 {
  margin-bottom: 0.75rem;
}

.mt-2 {
  margin-top: 0.5rem;
}

.mr-1 {
  margin-right: 0.25rem;
}

.-ml-1 {
  margin-left: -0.25rem;
}

.block {
  display: block;
}

.inline-block {
  display: inline-block;
}

.flex {
  display: flex;
}

.inline-flex {
  display: inline-flex;
}

.table {
  display: table;
}

.grid {
  display: grid;
}

.contents {
  display: contents;
}

.hidden {
  display: none;
}

.h-screen {
  height: 100vh;
}

.h-full {
  height: 100%;
}

.h-10 {
  height: 2.5rem;
}

.h-6 {
  height: 1.5rem;
}

.h-3 {
  height: 0.75rem;
}

.h-16 {
  height: 4rem;
}

.h-5 {
  height: 1.25rem;
}

.h-4 {
  height: 1rem;
}

.h-1 {
  height: 0.25rem;
}

.w-16 {
  width: 4rem;
}

.w-full {
  width: 100%;
}

.w-10 {
  width: 2.5rem;
}

.w-64 {
  width: 16rem;
}

.w-6 {
  width: 1.5rem;
}

.w-3 {
  width: 0.75rem;
}

.w-32 {
  width: 8rem;
}

.w-5 {
  width: 1.25rem;
}

.w-24 {
  width: 6rem;
}

.w-auto {
  width: auto;
}

.w-9 {
  width: 2.25rem;
}

.w-56 {
  width: 14rem;
}

.w-14 {
  width: 3.5rem;
}

.w-20 {
  width: 5rem;
}

.w-12 {
  width: 3rem;
}

.min-w-0 {
  min-width: 0px;
}

.flex-auto {
  flex: 1 1 auto;
}

.flex-none {
  flex: none;
}

.flex-1 {
  flex: 1 1 0%;
}

.flex-shrink-0 {
  flex-shrink: 0;
}

.flex-shrink {
  flex-shrink: 1;
}

.flex-grow {
  flex-grow: 1;
}

.grow-0 {
  flex-grow: 0;
}

.grow {
  flex-grow: 1;
}

.border-collapse {
  border-collapse: collapse;
}

.transform {
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

@-webkit-keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  -webkit-animation: spin 1s linear infinite;
          animation: spin 1s linear infinite;
}

.cursor-pointer {
  cursor: pointer;
}

.resize {
  resize: both;
}

.grid-cols-3 {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.flex-col {
  flex-direction: column;
}

.flex-wrap {
  flex-wrap: wrap;
}

.items-end {
  align-items: flex-end;
}

.items-center {
  align-items: center;
}

.justify-center {
  justify-content: center;
}

.justify-between {
  justify-content: space-between;
}

.gap-4 {
  gap: 1rem;
}

.space-y-2 > :not([hidden]) ~ :not([hidden]) {
  --tw-space-y-reverse: 0;
  margin-top: calc(0.5rem * calc(1 - var(--tw-space-y-reverse)));
  margin-bottom: calc(0.5rem * var(--tw-space-y-reverse));
}

.divide-y > :not([hidden]) ~ :not([hidden]) {
  --tw-divide-y-reverse: 0;
  border-top-width: calc(1px * calc(1 - var(--tw-divide-y-reverse)));
  border-bottom-width: calc(1px * var(--tw-divide-y-reverse));
}

.divide-blue-200 > :not([hidden]) ~ :not([hidden]) {
  --tw-divide-opacity: 1;
  border-color: rgb(191 219 254 / var(--tw-divide-opacity));
}

.self-center {
  align-self: center;
}

.overflow-hidden {
  overflow: hidden;
}

.overflow-y-auto {
  overflow-y: auto;
}

.whitespace-nowrap {
  white-space: nowrap;
}

.rounded {
  border-radius: 0.25rem;
}

.rounded-full {
  border-radius: 9999px;
}

.rounded-lg {
  border-radius: 0.5rem;
}

.rounded-none {
  border-radius: 0px;
}

.rounded-md {
  border-radius: 0.375rem;
}

.rounded-l-md {
  border-top-left-radius: 0.375rem;
  border-bottom-left-radius: 0.375rem;
}

.rounded-r-lg {
  border-top-right-radius: 0.5rem;
  border-bottom-right-radius: 0.5rem;
}

.border {
  border-width: 1px;
}

.border-4 {
  border-width: 4px;
}

.border-b-2 {
  border-bottom-width: 2px;
}

.border-b {
  border-bottom-width: 1px;
}

.border-r-0 {
  border-right-width: 0px;
}

.border-l-4 {
  border-left-width: 4px;
}

.border-b-4 {
  border-bottom-width: 4px;
}

.border-sushi-500 {
  --tw-border-opacity: 1;
  border-color: rgb(122 174 54 / var(--tw-border-opacity));
}

.border-blue-500 {
  --tw-border-opacity: 1;
  border-color: rgb(59 130 246 / var(--tw-border-opacity));
}

.border-sushi-200 {
  --tw-border-opacity: 1;
  border-color: rgb(222 235 205 / var(--tw-border-opacity));
}

.border-gray-100 {
  --tw-border-opacity: 1;
  border-color: rgb(243 244 246 / var(--tw-border-opacity));
}

.border-gray-200 {
  --tw-border-opacity: 1;
  border-color: rgb(229 231 235 / var(--tw-border-opacity));
}

.border-gray-500 {
  --tw-border-opacity: 1;
  border-color: rgb(107 114 128 / var(--tw-border-opacity));
}

.border-green-500 {
  --tw-border-opacity: 1;
  border-color: rgb(34 197 94 / var(--tw-border-opacity));
}

.border-chetwode-blue-600 {
  --tw-border-opacity: 1;
  border-color: rgb(134 122 193 / var(--tw-border-opacity));
}

.border-gray-300 {
  --tw-border-opacity: 1;
  border-color: rgb(209 213 219 / var(--tw-border-opacity));
}

.border-slate-300 {
  --tw-border-opacity: 1;
  border-color: rgb(203 213 225 / var(--tw-border-opacity));
}

.border-indigo-500 {
  --tw-border-opacity: 1;
  border-color: rgb(99 102 241 / var(--tw-border-opacity));
}

.border-wasabi-500 {
  --tw-border-opacity: 1;
  border-color: rgb(132 157 62 / var(--tw-border-opacity));
}

.bg-half-baked-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(135 199 215 / var(--tw-bg-opacity));
}

.bg-half-baked-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(122 179 194 / var(--tw-bg-opacity));
}

.bg-fountain-blue-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(82 173 179 / var(--tw-bg-opacity));
}

.bg-fountain-blue-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(74 156 161 / var(--tw-bg-opacity));
}

.bg-carissma-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(234 135 175 / var(--tw-bg-opacity));
}

.bg-carissma-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(211 122 158 / var(--tw-bg-opacity));
}

.bg-earls-green-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(217 196 61 / var(--tw-bg-opacity));
}

.bg-earls-green-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(195 176 55 / var(--tw-bg-opacity));
}

.bg-yellow-orange-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(243 164 60 / var(--tw-bg-opacity));
}

.bg-yellow-orange-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(219 148 54 / var(--tw-bg-opacity));
}

.bg-wasabi-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(132 157 62 / var(--tw-bg-opacity));
}

.bg-wasabi-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(119 141 56 / var(--tw-bg-opacity));
}

.bg-lilac-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(182 161 203 / var(--tw-bg-opacity));
}

.bg-lilac-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(164 145 183 / var(--tw-bg-opacity));
}

.bg-portage-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(135 149 214 / var(--tw-bg-opacity));
}

.bg-portage-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(122 134 193 / var(--tw-bg-opacity));
}

.bg-glacier-700 {
  --tw-bg-opacity: 1;
  background-color: rgb(101 142 161 / var(--tw-bg-opacity));
}

.bg-glacier-900 {
  --tw-bg-opacity: 1;
  background-color: rgb(66 93 105 / var(--tw-bg-opacity));
}

.bg-white {
  --tw-bg-opacity: 1;
  background-color: rgb(255 255 255 / var(--tw-bg-opacity));
}

.bg-red-100 {
  --tw-bg-opacity: 1;
  background-color: rgb(254 226 226 / var(--tw-bg-opacity));
}

.bg-gray-50 {
  --tw-bg-opacity: 1;
  background-color: rgb(249 250 251 / var(--tw-bg-opacity));
}

.bg-gray-200 {
  --tw-bg-opacity: 1;
  background-color: rgb(229 231 235 / var(--tw-bg-opacity));
}

.bg-blue-200 {
  --tw-bg-opacity: 1;
  background-color: rgb(191 219 254 / var(--tw-bg-opacity));
}

.bg-blue-700 {
  --tw-bg-opacity: 1;
  background-color: rgb(29 78 216 / var(--tw-bg-opacity));
}

.bg-portage-700 {
  --tw-bg-opacity: 1;
  background-color: rgb(101 112 161 / var(--tw-bg-opacity));
}

.bg-glacier-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(122 170 193 / var(--tw-bg-opacity));
}

.bg-glacier-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(135 189 214 / var(--tw-bg-opacity));
}

.bg-monte-carlo-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(135 214 206 / var(--tw-bg-opacity));
}

.bg-monte-carlo-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(122 193 185 / var(--tw-bg-opacity));
}

.bg-feijoa-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(149 214 135 / var(--tw-bg-opacity));
}

.bg-feijoa-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(134 193 122 / var(--tw-bg-opacity));
}

.bg-deco-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(190 214 135 / var(--tw-bg-opacity));
}

.bg-deco-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(171 193 122 / var(--tw-bg-opacity));
}

.bg-tan-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(214 176 135 / var(--tw-bg-opacity));
}

.bg-tan-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(193 158 122 / var(--tw-bg-opacity));
}

.bg-sushi-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(122 174 54 / var(--tw-bg-opacity));
}

.bg-sushi-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(110 157 49 / var(--tw-bg-opacity));
}

.bg-silk-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(194 175 163 / var(--tw-bg-opacity));
}

.bg-silk-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(175 158 147 / var(--tw-bg-opacity));
}

.bg-havelock-blue-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(81 164 245 / var(--tw-bg-opacity));
}

.bg-havelock-blue-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(73 148 221 / var(--tw-bg-opacity));
}

.bg-plum-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(135 73 137 / var(--tw-bg-opacity));
}

.bg-plum-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(122 66 123 / var(--tw-bg-opacity));
}

.bg-sushi-700 {
  --tw-bg-opacity: 1;
  background-color: rgb(92 131 41 / var(--tw-bg-opacity));
}

.bg-green-50 {
  --tw-bg-opacity: 1;
  background-color: rgb(240 253 244 / var(--tw-bg-opacity));
}

.bg-chetwode-blue-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(134 122 193 / var(--tw-bg-opacity));
}

.bg-chetwode-blue-700 {
  --tw-bg-opacity: 1;
  background-color: rgb(112 101 161 / var(--tw-bg-opacity));
}

.bg-slate-700 {
  --tw-bg-opacity: 1;
  background-color: rgb(51 65 85 / var(--tw-bg-opacity));
}

.bg-slate-900 {
  --tw-bg-opacity: 1;
  background-color: rgb(15 23 42 / var(--tw-bg-opacity));
}

.bg-pink-200 {
  --tw-bg-opacity: 1;
  background-color: rgb(251 207 232 / var(--tw-bg-opacity));
}

.bg-slate-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(100 116 139 / var(--tw-bg-opacity));
}

.bg-gray-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(107 114 128 / var(--tw-bg-opacity));
}

.bg-zinc-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(113 113 122 / var(--tw-bg-opacity));
}

.bg-neutral-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(115 115 115 / var(--tw-bg-opacity));
}

.bg-stone-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(120 113 108 / var(--tw-bg-opacity));
}

.bg-red-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(239 68 68 / var(--tw-bg-opacity));
}

.bg-orange-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(249 115 22 / var(--tw-bg-opacity));
}

.bg-amber-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(245 158 11 / var(--tw-bg-opacity));
}

.bg-yellow-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(234 179 8 / var(--tw-bg-opacity));
}

.bg-lime-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(132 204 22 / var(--tw-bg-opacity));
}

.bg-green-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(34 197 94 / var(--tw-bg-opacity));
}

.bg-emerald-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(16 185 129 / var(--tw-bg-opacity));
}

.bg-teal-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(20 184 166 / var(--tw-bg-opacity));
}

.bg-cyan-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(6 182 212 / var(--tw-bg-opacity));
}

.bg-sky-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(14 165 233 / var(--tw-bg-opacity));
}

.bg-blue-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(59 130 246 / var(--tw-bg-opacity));
}

.bg-indigo-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(99 102 241 / var(--tw-bg-opacity));
}

.bg-violet-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(139 92 246 / var(--tw-bg-opacity));
}

.bg-purple-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(168 85 247 / var(--tw-bg-opacity));
}

.bg-fuchsia-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(217 70 239 / var(--tw-bg-opacity));
}

.bg-pink-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(236 72 153 / var(--tw-bg-opacity));
}

.bg-rose-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(244 63 94 / var(--tw-bg-opacity));
}

.bg-slate-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(71 85 105 / var(--tw-bg-opacity));
}

.bg-gray-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(75 85 99 / var(--tw-bg-opacity));
}

.bg-zinc-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(82 82 91 / var(--tw-bg-opacity));
}

.bg-neutral-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(82 82 82 / var(--tw-bg-opacity));
}

.bg-stone-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(87 83 78 / var(--tw-bg-opacity));
}

.bg-red-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(220 38 38 / var(--tw-bg-opacity));
}

.bg-orange-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(234 88 12 / var(--tw-bg-opacity));
}

.bg-amber-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(217 119 6 / var(--tw-bg-opacity));
}

.bg-yellow-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(202 138 4 / var(--tw-bg-opacity));
}

.bg-lime-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(101 163 13 / var(--tw-bg-opacity));
}

.bg-green-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(22 163 74 / var(--tw-bg-opacity));
}

.bg-emerald-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(5 150 105 / var(--tw-bg-opacity));
}

.bg-teal-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(13 148 136 / var(--tw-bg-opacity));
}

.bg-cyan-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(8 145 178 / var(--tw-bg-opacity));
}

.bg-sky-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(2 132 199 / var(--tw-bg-opacity));
}

.bg-blue-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(37 99 235 / var(--tw-bg-opacity));
}

.bg-indigo-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(79 70 229 / var(--tw-bg-opacity));
}

.bg-violet-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(124 58 237 / var(--tw-bg-opacity));
}

.bg-purple-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(147 51 234 / var(--tw-bg-opacity));
}

.bg-fuchsia-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(192 38 211 / var(--tw-bg-opacity));
}

.bg-pink-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(219 39 119 / var(--tw-bg-opacity));
}

.bg-rose-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(225 29 72 / var(--tw-bg-opacity));
}

.bg-opal-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(163 194 191 / var(--tw-bg-opacity));
}

.bg-opal-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(147 175 172 / var(--tw-bg-opacity));
}

.bg-shocking-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(214 135 176 / var(--tw-bg-opacity));
}

.bg-lavender-rose-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(214 135 208 / var(--tw-bg-opacity));
}

.bg-lavender-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(176 135 214 / var(--tw-bg-opacity));
}

.bg-chetwode-blue-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(149 135 214 / var(--tw-bg-opacity));
}

.bg-algae-green-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(135 214 167 / var(--tw-bg-opacity));
}

.bg-putty-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(214 200 135 / var(--tw-bg-opacity));
}

.bg-new-york-pink-500 {
  --tw-bg-opacity: 1;
  background-color: rgb(214 135 135 / var(--tw-bg-opacity));
}

.bg-shocking-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(193 122 158 / var(--tw-bg-opacity));
}

.bg-lavender-rose-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(193 122 187 / var(--tw-bg-opacity));
}

.bg-lavender-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(158 122 193 / var(--tw-bg-opacity));
}

.bg-algae-green-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(122 193 150 / var(--tw-bg-opacity));
}

.bg-putty-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(193 180 122 / var(--tw-bg-opacity));
}

.bg-new-york-pink-600 {
  --tw-bg-opacity: 1;
  background-color: rgb(193 122 122 / var(--tw-bg-opacity));
}

.bg-gray-100 {
  --tw-bg-opacity: 1;
  background-color: rgb(243 244 246 / var(--tw-bg-opacity));
}

.fill-slate-300 {
  fill: #cbd5e1;
}

.fill-half-baked-300 {
  fill: #cfe9ef;
}

.fill-slate-600 {
  fill: #475569;
}

.fill-gray-600 {
  fill: #4b5563;
}

.fill-zinc-600 {
  fill: #52525b;
}

.fill-neutral-600 {
  fill: #525252;
}

.fill-stone-600 {
  fill: #57534e;
}

.fill-red-600 {
  fill: #dc2626;
}

.fill-orange-600 {
  fill: #ea580c;
}

.fill-amber-600 {
  fill: #d97706;
}

.fill-yellow-600 {
  fill: #ca8a04;
}

.fill-lime-600 {
  fill: #65a30d;
}

.fill-green-600 {
  fill: #16a34a;
}

.fill-emerald-600 {
  fill: #059669;
}

.fill-teal-600 {
  fill: #0d9488;
}

.fill-cyan-600 {
  fill: #0891b2;
}

.fill-sky-600 {
  fill: #0284c7;
}

.fill-blue-600 {
  fill: #2563eb;
}

.fill-indigo-600 {
  fill: #4f46e5;
}

.fill-violet-600 {
  fill: #7c3aed;
}

.fill-purple-600 {
  fill: #9333ea;
}

.fill-fuchsia-600 {
  fill: #c026d3;
}

.fill-pink-600 {
  fill: #db2777;
}

.fill-rose-600 {
  fill: #e11d48;
}

.fill-wasabi-600 {
  fill: #778d38;
}

.fill-plum-600 {
  fill: #7a427b;
}

.fill-lilac-600 {
  fill: #a491b7;
}

.fill-yellow-orange-600 {
  fill: #db9436;
}

.fill-earls-green-600 {
  fill: #c3b037;
}

.fill-carissma-600 {
  fill: #d37a9e;
}

.fill-fountain-blue-600 {
  fill: #4a9ca1;
}

.fill-half-baked-600 {
  fill: #7ab3c2;
}

.fill-sushi-600 {
  fill: #6e9d31;
}

.fill-silk-600 {
  fill: #af9e93;
}

.fill-opal-600 {
  fill: #93afac;
}

.fill-havelock-blue-600 {
  fill: #4994dd;
}

.fill-shocking-600 {
  fill: #c17a9e;
}

.fill-lavender-rose-600 {
  fill: #c17abb;
}

.fill-lavender-600 {
  fill: #9e7ac1;
}

.fill-chetwode-blue-600 {
  fill: #867ac1;
}

.fill-portage-600 {
  fill: #7a86c1;
}

.fill-glacier-600 {
  fill: #7aaac1;
}

.fill-monte-carlo-600 {
  fill: #7ac1b9;
}

.fill-algae-green-600 {
  fill: #7ac196;
}

.fill-feijoa-600 {
  fill: #86c17a;
}

.fill-deco-600 {
  fill: #abc17a;
}

.fill-putty-600 {
  fill: #c1b47a;
}

.fill-tan-600 {
  fill: #c19e7a;
}

.fill-new-york-pink-600 {
  fill: #c17a7a;
}

.stroke-half-baked-700 {
  stroke: #6595a1;
}

.stroke-fountain-blue-700 {
  stroke: #3e8286;
}

.stroke-carissma-700 {
  stroke: #b06583;
}

.stroke-earls-green-700 {
  stroke: #a3932e;
}

.stroke-yellow-orange-700 {
  stroke: #b67b2d;
}

.stroke-wasabi-700 {
  stroke: #63762f;
}

.stroke-lilac-700 {
  stroke: #897998;
}

.stroke-portage-700 {
  stroke: #6570a1;
}

.stroke-stone-700 {
  stroke: #44403c;
}

.stroke-portage-800 {
  stroke: #515980;
}

.stroke-glacier-800 {
  stroke: #517180;
}

.stroke-glacier-700 {
  stroke: #658ea1;
}

.stroke-monte-carlo-700 {
  stroke: #65a19b;
}

.stroke-feijoa-700 {
  stroke: #70a165;
}

.stroke-deco-700 {
  stroke: #8fa165;
}

.stroke-tan-700 {
  stroke: #a18465;
}

.stroke-sushi-700 {
  stroke: #5c8329;
}

.stroke-silk-700 {
  stroke: #92837a;
}

.stroke-havelock-blue-700 {
  stroke: #3d7bb8;
}

.stroke-plum-700 {
  stroke: #653767;
}

.stroke-half-baked-800 {
  stroke: #517781;
}

.stroke-half-baked-900 {
  stroke: #426269;
}

.stroke-half-baked-300 {
  stroke: #cfe9ef;
}

.stroke-slate-100 {
  stroke: #f1f5f9;
}

.stroke-gray-100 {
  stroke: #f3f4f6;
}

.stroke-gray-800 {
  stroke: #1f2937;
}

.stroke-1 {
  stroke-width: 1;
}

.p-1 {
  padding: 0.25rem;
}

.p-2 {
  padding: 0.5rem;
}

.p-3 {
  padding: 0.75rem;
}

.py-1 {
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
}

.px-2 {
  padding-left: 0.5rem;
  padding-right: 0.5rem;
}

.px-5 {
  padding-left: 1.25rem;
  padding-right: 1.25rem;
}

.py-2\.5 {
  padding-top: 0.625rem;
  padding-bottom: 0.625rem;
}

.py-2 {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.py-4 {
  padding-top: 1rem;
  padding-bottom: 1rem;
}

.px-3 {
  padding-left: 0.75rem;
  padding-right: 0.75rem;
}

.pt-16 {
  padding-top: 4rem;
}

.pr-4 {
  padding-right: 1rem;
}

.pl-11 {
  padding-left: 2.75rem;
}

.pt-28 {
  padding-top: 7rem;
}

.pt-40 {
  padding-top: 10rem;
}

.pt-32 {
  padding-top: 8rem;
}

.pt-64 {
  padding-top: 16rem;
}

.pt-4 {
  padding-top: 1rem;
}

.pl-3 {
  padding-left: 0.75rem;
}

.pl-2 {
  padding-left: 0.5rem;
}

.pl-9 {
  padding-left: 2.25rem;
}

.pr-3 {
  padding-right: 0.75rem;
}

.pt-7 {
  padding-top: 1.75rem;
}

.pb-5 {
  padding-bottom: 1.25rem;
}

.pt-6 {
  padding-top: 1.5rem;
}

.pb-4 {
  padding-bottom: 1rem;
}

.text-left {
  text-align: left;
}

.text-center {
  text-align: center;
}

.text-lg {
  font-size: 1.125rem;
  line-height: 1.75rem;
}

.text-xs {
  font-size: 0.75rem;
  line-height: 1rem;
}

.text-sm {
  font-size: 0.875rem;
  line-height: 1.25rem;
}

.text-base {
  font-size: 1rem;
  line-height: 1.5rem;
}

.text-xl {
  font-size: 1.25rem;
  line-height: 1.75rem;
}

.font-bold {
  font-weight: 700;
}

.font-semibold {
  font-weight: 600;
}

.font-normal {
  font-weight: 400;
}

.font-medium {
  font-weight: 500;
}

.font-thin {
  font-weight: 100;
}

.uppercase {
  text-transform: uppercase;
}

.leading-6 {
  line-height: 1.5rem;
}

.leading-3 {
  line-height: .75rem;
}

.text-half-baked-50 {
  --tw-text-opacity: 1;
  color: rgb(249 252 253 / var(--tw-text-opacity));
}

.text-fountain-blue-50 {
  --tw-text-opacity: 1;
  color: rgb(246 251 251 / var(--tw-text-opacity));
}

.text-carissma-50 {
  --tw-text-opacity: 1;
  color: rgb(254 249 251 / var(--tw-text-opacity));
}

.text-earls-green-50 {
  --tw-text-opacity: 1;
  color: rgb(253 252 245 / var(--tw-text-opacity));
}

.text-yellow-orange-50 {
  --tw-text-opacity: 1;
  color: rgb(254 250 245 / var(--tw-text-opacity));
}

.text-wasabi-50 {
  --tw-text-opacity: 1;
  color: rgb(249 250 245 / var(--tw-text-opacity));
}

.text-lilac-50 {
  --tw-text-opacity: 1;
  color: rgb(251 250 252 / var(--tw-text-opacity));
}

.text-portage-50 {
  --tw-text-opacity: 1;
  color: rgb(249 250 253 / var(--tw-text-opacity));
}

.text-glacier-50 {
  --tw-text-opacity: 1;
  color: rgb(249 252 253 / var(--tw-text-opacity));
}

.text-white {
  --tw-text-opacity: 1;
  color: rgb(255 255 255 / var(--tw-text-opacity));
}

.text-gray-800 {
  --tw-text-opacity: 1;
  color: rgb(31 41 55 / var(--tw-text-opacity));
}

.text-gray-900 {
  --tw-text-opacity: 1;
  color: rgb(17 24 39 / var(--tw-text-opacity));
}

.text-gray-500 {
  --tw-text-opacity: 1;
  color: rgb(107 114 128 / var(--tw-text-opacity));
}

.text-blue-600 {
  --tw-text-opacity: 1;
  color: rgb(37 99 235 / var(--tw-text-opacity));
}

.text-gray-700 {
  --tw-text-opacity: 1;
  color: rgb(55 65 81 / var(--tw-text-opacity));
}

.text-monte-carlo-50 {
  --tw-text-opacity: 1;
  color: rgb(249 253 253 / var(--tw-text-opacity));
}

.text-feijoa-50 {
  --tw-text-opacity: 1;
  color: rgb(250 253 249 / var(--tw-text-opacity));
}

.text-deco-50 {
  --tw-text-opacity: 1;
  color: rgb(252 253 249 / var(--tw-text-opacity));
}

.text-tan-50 {
  --tw-text-opacity: 1;
  color: rgb(253 251 249 / var(--tw-text-opacity));
}

.text-sushi-50 {
  --tw-text-opacity: 1;
  color: rgb(248 251 245 / var(--tw-text-opacity));
}

.text-silk-50 {
  --tw-text-opacity: 1;
  color: rgb(252 251 250 / var(--tw-text-opacity));
}

.text-havelock-blue-50 {
  --tw-text-opacity: 1;
  color: rgb(246 250 255 / var(--tw-text-opacity));
}

.text-plum-50 {
  --tw-text-opacity: 1;
  color: rgb(249 246 249 / var(--tw-text-opacity));
}

.text-green-700 {
  --tw-text-opacity: 1;
  color: rgb(21 128 61 / var(--tw-text-opacity));
}

.text-green-900 {
  --tw-text-opacity: 1;
  color: rgb(20 83 45 / var(--tw-text-opacity));
}

.text-green-600 {
  --tw-text-opacity: 1;
  color: rgb(22 163 74 / var(--tw-text-opacity));
}

.text-chetwode-blue-300 {
  --tw-text-opacity: 1;
  color: rgb(213 207 239 / var(--tw-text-opacity));
}

.text-chetwode-blue-400 {
  --tw-text-opacity: 1;
  color: rgb(181 171 226 / var(--tw-text-opacity));
}

.text-slate-50 {
  --tw-text-opacity: 1;
  color: rgb(248 250 252 / var(--tw-text-opacity));
}

.text-pink-600 {
  --tw-text-opacity: 1;
  color: rgb(219 39 119 / var(--tw-text-opacity));
}

.text-gray-50 {
  --tw-text-opacity: 1;
  color: rgb(249 250 251 / var(--tw-text-opacity));
}

.text-zinc-50 {
  --tw-text-opacity: 1;
  color: rgb(250 250 250 / var(--tw-text-opacity));
}

.text-neutral-50 {
  --tw-text-opacity: 1;
  color: rgb(250 250 250 / var(--tw-text-opacity));
}

.text-stone-50 {
  --tw-text-opacity: 1;
  color: rgb(250 250 249 / var(--tw-text-opacity));
}

.text-red-50 {
  --tw-text-opacity: 1;
  color: rgb(254 242 242 / var(--tw-text-opacity));
}

.text-orange-50 {
  --tw-text-opacity: 1;
  color: rgb(255 247 237 / var(--tw-text-opacity));
}

.text-amber-50 {
  --tw-text-opacity: 1;
  color: rgb(255 251 235 / var(--tw-text-opacity));
}

.text-yellow-50 {
  --tw-text-opacity: 1;
  color: rgb(254 252 232 / var(--tw-text-opacity));
}

.text-lime-50 {
  --tw-text-opacity: 1;
  color: rgb(247 254 231 / var(--tw-text-opacity));
}

.text-green-50 {
  --tw-text-opacity: 1;
  color: rgb(240 253 244 / var(--tw-text-opacity));
}

.text-emerald-50 {
  --tw-text-opacity: 1;
  color: rgb(236 253 245 / var(--tw-text-opacity));
}

.text-teal-50 {
  --tw-text-opacity: 1;
  color: rgb(240 253 250 / var(--tw-text-opacity));
}

.text-cyan-50 {
  --tw-text-opacity: 1;
  color: rgb(236 254 255 / var(--tw-text-opacity));
}

.text-sky-50 {
  --tw-text-opacity: 1;
  color: rgb(240 249 255 / var(--tw-text-opacity));
}

.text-blue-50 {
  --tw-text-opacity: 1;
  color: rgb(239 246 255 / var(--tw-text-opacity));
}

.text-indigo-50 {
  --tw-text-opacity: 1;
  color: rgb(238 242 255 / var(--tw-text-opacity));
}

.text-violet-50 {
  --tw-text-opacity: 1;
  color: rgb(245 243 255 / var(--tw-text-opacity));
}

.text-purple-50 {
  --tw-text-opacity: 1;
  color: rgb(250 245 255 / var(--tw-text-opacity));
}

.text-fuchsia-50 {
  --tw-text-opacity: 1;
  color: rgb(253 244 255 / var(--tw-text-opacity));
}

.text-pink-50 {
  --tw-text-opacity: 1;
  color: rgb(253 242 248 / var(--tw-text-opacity));
}

.text-rose-50 {
  --tw-text-opacity: 1;
  color: rgb(255 241 242 / var(--tw-text-opacity));
}

.text-opal-50 {
  --tw-text-opacity: 1;
  color: rgb(250 252 252 / var(--tw-text-opacity));
}

.text-shocking-50 {
  --tw-text-opacity: 1;
  color: rgb(253 249 251 / var(--tw-text-opacity));
}

.text-lavender-rose-50 {
  --tw-text-opacity: 1;
  color: rgb(253 249 253 / var(--tw-text-opacity));
}

.text-lavender-50 {
  --tw-text-opacity: 1;
  color: rgb(251 249 253 / var(--tw-text-opacity));
}

.text-chetwode-blue-50 {
  --tw-text-opacity: 1;
  color: rgb(250 249 253 / var(--tw-text-opacity));
}

.text-algae-green-50 {
  --tw-text-opacity: 1;
  color: rgb(249 253 251 / var(--tw-text-opacity));
}

.text-putty-50 {
  --tw-text-opacity: 1;
  color: rgb(253 252 249 / var(--tw-text-opacity));
}

.text-new-york-pink-50 {
  --tw-text-opacity: 1;
  color: rgb(253 249 249 / var(--tw-text-opacity));
}

.underline {
  -webkit-text-decoration-line: underline;
          text-decoration-line: underline;
}

.no-underline {
  -webkit-text-decoration-line: none;
          text-decoration-line: none;
}

.opacity-25 {
  opacity: 0.25;
}

.opacity-75 {
  opacity: 0.75;
}

.shadow-sm {
  --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}

.outline {
  outline-style: solid;
}

.blur {
  --tw-blur: blur(8px);
  filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow);
}

.filter {
  filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow);
}

.transition {
  transition-property: color, background-color, border-color, fill, stroke, opacity, box-shadow, transform, filter, -webkit-text-decoration-color, -webkit-backdrop-filter;
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter, -webkit-text-decoration-color, -webkit-backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

.duration-75 {
  transition-duration: 75ms;
}

.ease-in {
  transition-timing-function: cubic-bezier(0.4, 0, 1, 1);
}

.last\:mb-0:last-child {
  margin-bottom: 0px;
}

.hover\:bg-glacier-800:hover {
  --tw-bg-opacity: 1;
  background-color: rgb(81 113 128 / var(--tw-bg-opacity));
}

.hover\:underline:hover {
  -webkit-text-decoration-line: underline;
          text-decoration-line: underline;
}

.focus\:outline-none:focus {
  outline: 2px solid transparent;
  outline-offset: 2px;
}

.focus\:ring-4:focus {
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(4px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}

.focus\:ring-glacier-300:focus {
  --tw-ring-opacity: 1;
  --tw-ring-color: rgb(207 229 239 / var(--tw-ring-opacity));
}

@media (min-width: 640px) {
  .sm\:px-4 {
    padding-left: 1rem;
    padding-right: 1rem;
  }
}

@media (min-width: 768px) {
  .md\:my-0 {
    margin-top: 0px;
    margin-bottom: 0px;
  }

  .md\:mt-0 {
    margin-top: 0px;
  }

  .md\:block {
    display: block;
  }

  .md\:grid {
    display: grid;
  }

  .md\:w-2\/3 {
    width: 66.666667%;
  }

  .md\:w-auto {
    width: auto;
  }

  .md\:grid-cols-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }

  .md\:flex-row {
    flex-direction: row;
  }

  .md\:gap-6 {
    gap: 1.5rem;
  }

  .md\:space-x-8 > :not([hidden]) ~ :not([hidden]) {
    --tw-space-x-reverse: 0;
    margin-right: calc(2rem * var(--tw-space-x-reverse));
    margin-left: calc(2rem * calc(1 - var(--tw-space-x-reverse)));
  }

  .md\:text-sm {
    font-size: 0.875rem;
    line-height: 1.25rem;
  }

  .md\:font-medium {
    font-weight: 500;
  }
}
