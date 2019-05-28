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

# Get the Dynamics credentials and endpoint
readParameter "DYNAMICS_ENDPOINT - Please provide the URL of the Dynamics endpoint.  You MUST supply a value." DYNAMICS_ENDPOINT ""
readParameter "DYNAMICS_USERNAME - Please provide the Dynamics username:" DYNAMICS_USERNAME "" 
readParameter "DYNAMICS_PASSWORD - Please provide the Dynamics password:" DYNAMICS_PASSWORD "" 
readParameter "DYNAMICS_DOMAIN - Please provide the Dynamics domain:" DYNAMICS_DOMAIN "" 

SPECIALDEPLOYPARMS="--param-file=${_overrideParamFile}"
echo ${SPECIALDEPLOYPARMS}

