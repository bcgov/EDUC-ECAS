// JavaScript source code
function replaceString(stringToSearch, stringToFind, replaceWith) {
    return stringToSearch.replace(stringToFind, replaceWith);
}

function clearBase64StringPrefix() {
    var signatureBase64 = Xrm.Page.getAttribute("educ_approvalsignature").getValue();
   if(signatureBase64 !==null)
   {
         console.log(signatureBase64);
         var wordBase64 = replaceString(signatureBase64, 'data:image/png;base64,', '');
         console.log(wordBase64);
        Xrm.Page.getAttribute("educ_approvalauthoritysignaturetext").setValue(wordBase64);
   }
}