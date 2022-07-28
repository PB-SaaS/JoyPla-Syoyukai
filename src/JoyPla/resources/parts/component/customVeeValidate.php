

function getTwoFieldRequiredParam(params){
  if (!params) {
      return {
          targetField: '',
          targetValue: '',
      };
  }
  if (Array.isArray(params)) {
      return { targetField: params[0], targetValue: params[1] };
  }
  return params;
}

//２フィールド間にまたがる入力必須
const twoFieldRequiredValidator =  (value, params , ctx) => {
      
      const { targetField, targetValue } = getTwoFieldRequiredParam(params);
      if (Array.isArray(value)) {
          return value.every(val => !!twoFieldRequiredValidator(val, { targetField, targetValue }));
      }

      let isEmpty1 = ( !value || !value.length );
      let isEmpty2 = ( !targetValue || !targetValue);
      
      if( isEmpty1 && isEmpty2 || !isEmpty1 && !isEmpty2 ){
        return true;
      }

      return `${ctx.field}を入力した場合は${targetField}も入力してください`;
};

VeeValidate.defineRule('twoFieldRequired',twoFieldRequiredValidator);

VeeValidate.defineRule('lotnumber', value => {
  // Field is empty, should pass
  if( !value || !value.length ) { return true; }
  let regex = /^[a-zA-Z0-9!-/:-@¥[-`{-~]+$/;
  if (regex.test(value) && encodeURI(value).replace(/%../g, '*').length <= 20 ) {
    return true;
  }

  return false;
});

VeeValidate.configure({
  generateMessage: VeeValidateI18n.localize('ja', {
    messages: {
      lotnumber: '{field}は数字記号アルファベット20文字以内で入力してください',
    },
  }),
});
