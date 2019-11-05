// JavaScript source code
function replaceString(stringToSearch, stringToFind, replaceWith) {
    return stringToSearch.replace(stringToFind, replaceWith);
}

function clearBase64StringPrefix(executionContext, imageFieldToParse, textFieldtoPopulate) {
   var formContext = executionContext.getFormContext();
   var imageFieldValue = formContext.getAttribute(imageFieldToParse).getValue();
   var textField = formContext.getAttribute(textFieldtoPopulate);

   if(imageFieldValue !==null)
   {
         console.log(imageFieldValue );
         var wordBase64 = replaceString(imageFieldValue , 'data:image/png;base64,', '');
         console.log(wordBase64);
         textField.setValue(wordBase64);
   }
}