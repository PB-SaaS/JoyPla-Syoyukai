@tailwind base;
@tailwind components;
@tailwind utilities;

.text-xxs {
  font-size : 0.75rem;
  line-height: 1rem;
}


.uk-text-danger {
  @apply text-[#f0506e];
}

.uk-text-bold {
  @apply font-bold;
}

input[aria-invalid="true"] {
    @apply text-red-500 border-red-500 border;
}

[v-cloak] {
    display: none;
}

/**************************\
  Basic Modal Styles
\**************************/
  
.modal {
  font-family: -apple-system,BlinkMacSystemFont,avenir next,avenir,helvetica neue,helvetica,ubuntu,roboto,noto,segoe ui,arial,sans-serif;
}
  
.modal__overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
}
  
.modal__container {
  background-color: #fff;
  padding: 30px;
  max-width: 500px;
  max-height: 100vh;
  border-radius: 4px;
  overflow-y: auto;
  box-sizing: border-box;
}
  
.modal_large_container {
  background-color: #fff;
  padding: 30px;
  width: 100vw;
  height: 100vh;
  border-radius: 4px;
  overflow-y: auto;
  box-sizing: border-box;
}
  
.modal__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  }
  
.modal__title {
  margin-top: 0;
  margin-bottom: 0;
  font-weight: 600;
  font-size: 1.25rem;
  line-height: 1.25;
  color: #00449e;
  box-sizing: border-box;
}
  
.modal__close {
  background: transparent;
  border: 0;
}
  
.modal__header .modal__close:before { content: "\2715"; }
  
.modal__content {
  margin-top: 2rem;
  margin-bottom: 2rem;
  line-height: 1.5;
  color: rgba(0,0,0,.8);
}
.modal_large_content {
  line-height: 1.5;
  color: rgba(0,0,0,.8);
}
  
.modal__btn {
  font-size: .875rem;
  padding-left: 1rem;
  padding-right: 1rem;
  padding-top: .5rem;
  padding-bottom: .5rem;
  background-color: #e6e6e6;
  color: rgba(0,0,0,.8);
  border-radius: .25rem;
  border-style: none;
  border-width: 0;
  cursor: pointer;
  -webkit-appearance: button;
  text-transform: none;
  overflow: visible;
  line-height: 1.15;
  margin: 0;
  will-change: transform;
  -moz-osx-font-smoothing: grayscale;
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  -webkit-transform: translateZ(0);
  transform: translateZ(0);
  transition: -webkit-transform .25s ease-out;
  transition: transform .25s ease-out;
  transition: transform .25s ease-out,-webkit-transform .25s ease-out;
}
  
.modal__btn:focus, .modal__btn:hover {
  -webkit-transform: scale(1.05);
  transform: scale(1.05);
}
  
.modal__btn-primary {
  background-color: #00449e;
  color: #fff;
}
  
  
  
/**************************\
  Demo Animation Style
\**************************/
@keyframes mmfadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
  
@keyframes mmfadeOut {
  from { opacity: 1; }
  to { opacity: 0; }
}
  
@keyframes mmslideIn {
  from { transform: translateY(15%); }
  to { transform: translateY(0); }
}
  
@keyframes mmslideOut {
  from { transform: translateY(0); }
  to { transform: translateY(-10%); }
}
  
.micromodal-slide {
  display: none;
}
  
.micromodal-slide.is-open {
  display: block;
}
  
  
.micromodal-slide .modal__container,
.micromodal-slide .modal__overlay {
  will-change: transform;
}

.list-enter-active, .list-leave-active {
  transition: all 0.5s;
}
.list-enter, .list-leave-to /* .list-leave-active for below version 2.1.8 */ {
  opacity: 0;
  transform: translateX(30px);
}

.swal2-html-container {
  white-space: pre;
}

.inputChange {
  background-color: rgb(255, 204, 153);
}
@layer components {
  [type="checkbox"]:checked {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='%23fff' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
  }

  [type="radio"]:checked {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='%23fff' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
  }

  [type="checkbox"],
  [type="radio"] {
    @apply focus:ring-0 focus:ring-offset-0;
  }

  [type="checkbox"],
  [type="checkbox"]:checked,
  [type="checkbox"]:checked:hover,
  [type="checkbox"]:checked:focus,
  [type="checkbox"]:indeterminate:hover,
  [type="radio"],
  [type="radio"]:checked,
  [type="radio"]:checked:hover,
  [type="radio"]:checked:focus {
    @apply border-gray-300;
  }
}

.draggable {
  cursor: grab;
}

.draggable:active {
  cursor: grabbing;
}

.dragging {
  background-color: #eee;
}

