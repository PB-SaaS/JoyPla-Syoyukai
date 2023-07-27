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

//全角半角の区別をする文字数チェック
const strlen = (value, [limit] , ctx) => {
  // The field is empty so it should pass
  if (!value || !value.length) {
    return true;
  }

  let len = 0;
  for (let i = 0; i < value.length; i++) {
    (value[i].match(/[ -~]/)) ? len += 1 : len += 2;
  }

  if (len > limit) {
    return `${ctx.field}は全角${limit / 2}文字以内、半角${limit}文字以内で入力してください`;
  }
  return true;
};

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

VeeValidate.defineRule('lotdate', value => {
  // Field is empty, should pass
  if( !value || !value.length ) { return true; }
  let regex1 = /^[0-9]{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01])$/;
  let regex2 = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/;
  let regex3 = /^[0-9]{4}年(0[1-9]|1[0-2])月(0[1-9]|[12][0-9]|3[01])日$/;
  if (regex1.test(value) || regex2.test(value) || regex3.test(value)) {
    return true;
  }

  return false;
});

//２フィールド間同一値をNGとする
const notTwoFieldSameAsValidator =  (value, params , ctx) => {
      
      const { targetField, targetValue } = getTwoFieldRequiredParam(params);
      if (Array.isArray(value)) {
          return value.every(val => !!twoFieldRequiredValidator(val, { targetField, targetValue }));
      }

      let isEmpty1 = ( !value || !value.length );
      let isEmpty2 = ( !targetValue || !targetValue);

      if( value != targetValue){
        return true;
      }

      return `${targetField}と異なる値にしてください`;
};

VeeValidate.defineRule('notTwoFieldSameAs',notTwoFieldSameAsValidator);
VeeValidate.defineRule('twoFieldRequired',twoFieldRequiredValidator);
VeeValidate.defineRule('strlen',strlen);

VeeValidate.defineRule('lotnumber', value => {
  // Field is empty, should pass
  if( !value || !value.length ) { return true; }
  let regex = /^[a-zA-Z0-9!-/:-@¥[-`{-~]+$/;
  if (regex.test(value) && encodeURI(value).replace(/%../g, '*').length <= 20 ) {
    return true;
  }

  return false;
});



VeeValidate.defineRule('decimal', (value, params , ctx) => {
    const regex = new RegExp(`^-?\\d*(\\.\\d{1,${params[0]}})?$`);
    if(regex.test(value)){ return true; }
    return `${ctx.field}は小数点以下${params[0]}桁の数字でなければなりません`;
});


VeeValidate.configure({
  generateMessage: VeeValidateI18n.localize('ja', {
    messages: {
      lotnumber: '{field}は数字記号アルファベット20文字以内で入力してください',
      lotdate: '{field}のフォーマットが不正です。例：YYYY-MM-DD,YYYY/MM/DD,YYYY年MM月DD日',
    },
  }),
});
