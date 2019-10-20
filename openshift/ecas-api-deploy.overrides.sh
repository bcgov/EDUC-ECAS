# ========================================================================
# Special Deployment Parameters needed for the deployment
# ------------------------------------------------------------------------
# ========================================================================

printStatusMsg(){
  (
    _msg=${1}
    _yellow='\033[1;33m'
    _nc='\033[0m' # No Color
    printf "\n${_yellow}${_msg}\n${_nc}" >&2
  )
}

readParameter(){
  (
    _msg=${1}
    _paramName=${2}
    _defaultValue=${3}
    _encode=${4}

    _yellow='\033[1;33m'
    _nc='\033[0m' # No Color
    _message=$(echo -e "\n${_yellow}${_msg}\n${_nc}")

    read -r -p $"${_message}" ${_paramName}

    writeParameter "${_paramName}" "${_defaultValue}" "${_encode}"
  )
}

writeParameter(){
  (
    _paramName=${1}
    _defaultValue=${2}
    _encode=${3}

    if [ -z "${_encode}" ]; then
      echo "${_paramName}=${!_paramName:-${_defaultValue}}" >> ${_overrideParamFile}
    else
      # The key/value pair must be contained on a single line
      _encodedValue=$(echo -n "${!_paramName:-${_defaultValue}}"|base64 -w 0)
      echo "${_paramName}=${_encodedValue}" >> ${_overrideParamFile}
    fi
  )
}

toLower() {
  echo $(echo ${@} | tr '[:upper:]' '[:lower:]')
}

getOperation() {
  (
    echo $(toLower ${OPERATION})
  )
}

createOperation() {
  (
    action=$(getOperation)
    if [ ${action} = "create" ]; then
      return 0
    else
      return 1
    fi
  )
}

generateKey(){
  (
    _length=${1:-48}
    # Format can be `-base64` or `-hex`
    _format=${2:--base64}

    echo $(openssl rand ${_format} ${_length})
  )
}

generateUsername() {
  # Generate a random username ...
  _userName=User_$( generateKey | LC_CTYPE=C tr -dc 'a-zA-Z0-9' | fold -w 10   | head -n 1 )
  _userName=$(echo -n "${_userName}")
  echo ${_userName}
}

generatePassword() {
  # Generate a random password ...
  _password=$( generateKey | LC_CTYPE=C tr -dc 'a-zA-Z0-9_' | fold -w 20 | head -n 1 )
  _password=$(echo -n "${_password}")
  echo ${_password}
}

initialize(){
  # Define the name of the override param file.
  _scriptName=$(basename ${0%.*})
  export _overrideParamFile=${_scriptName}.param

  printStatusMsg "Initializing ${_scriptName} ..."

  # Remove any previous version of the file ...
  if [ -f ${_overrideParamFile} ]; then
    printStatusMsg "Removing previous copy of ${_overrideParamFile} ..."
    rm -f ${_overrideParamFile}
  fi
}

initialize

if createOperation; then
  # Get the Dynamics credentials and endpoint
  readParameter "DYNAMICS_ENDPOINT - Please provide the URL of the Dynamics endpoint.  You MUST supply a value." DYNAMICS_ENDPOINT ""
  readParameter "DYNAMICS_USERNAME - Please provide the Dynamics username:" DYNAMICS_USERNAME ""
  readParameter "DYNAMICS_PASSWORD - Please provide the Dynamics password:" DYNAMICS_PASSWORD ""
  readParameter "DYNAMICS_DOMAIN - Please provide the Dynamics domain:" DYNAMICS_DOMAIN ""
  readParameter "ECAS_API_USERNAME - Please provide the ECAS API username.  If one is not specified a random one will be generated." ECAS_API_USERNAME $(generateUsername)
  readParameter "ECAS_API_PASSWORD - Please provide the ECAS API password.  If one is not specified a random one will be generated." ECAS_API_PASSWORD $(generatePassword)
else
  # Secrets are removed from the configurations during update operations ...
  printStatusMsg "Update operation detected ...\nSkipping the prompts for DYNAMICS_ENDPOINT, DYNAMICS_USERNAME, DYNAMICS_PASSWORD, DYNAMICS_DOMAIN, ECAS_API_USERNAME, and ECAS_API_PASSWORD secrets ...\n"
  writeParameter "DYNAMICS_ENDPOINT" "prompt_skipped" "false"
  writeParameter "DYNAMICS_USERNAME" "prompt_skipped" "false"
  writeParameter "DYNAMICS_PASSWORD" "prompt_skipped" "false"
  writeParameter "DYNAMICS_DOMAIN" "prompt_skipped" "false"
  writeParameter "ECAS_API_USERNAME" "prompt_skipped" "false"
  writeParameter "ECAS_API_PASSWORD" "prompt_skipped" "false"
fi

SPECIALDEPLOYPARMS="--param-file=${_overrideParamFile}"
echo ${SPECIALDEPLOYPARMS}