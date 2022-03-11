#!/bin/bash
#
#
#java -jar custom_module.jar -t $TOKEN -s $SECRET $*

TARGET_DIR='src'
TARGET='NewJoyPla'
DATE=`date '+%y%m%d%H%M%S'`
LOG_OUT="logs/deploy.log"
ZIP_FILE="upload_file"


TOKEN=00011KB9HzJA6571fc2a62048af337abb32cbab1e0dfa3c8aadb
SECRET=f2bf4a8e7b3567fdba896429ea1c136e89320175

# joypla2_test
#TOKEN=00011Bki9kGk5abfd3697509e0f98372a184110aedfbd6d3163e
#SECRET=53b7ebda4372d64ff56a1f1c60bcf303744a4d62

# JoyPla_MCHdemo2
#TOKEN=00002G4B6HfFdf61bd46804474f793ccd0f663ab01cdce5951ab
#SECRET=f340f234d86f2a7a3c31d69c829531ce86d6f0ab

# JoyPla_staging
#TOKEN=00011B9A9Hjf0ef33b8cc05ac2d791c36eb37bf88040bb05417a
#SECRET=cdd9b6ab7cade2db2f038992fa4f18ad5a457dd2

# joypla2_staging
#TOKEN=00011BccYXk8b3df1177a31560a5c62bde2404820c64f992f1df
#SECRET=2b320a05108b15f4b627d01867b18d320b36741b

#JoyPla_Stery2
#TOKEN=00051ch0wBaH42eef83b68d011985615a2ed3886c947f42d57e2
#SECRET=94d8c9199dd27752c5ac9cd67ea5aceb124b9313

#JoyPla_alpha
#TOKEN=000736Ah3I7I6064ebd817a3b53cf127cefa2b0ddd4eea7816ac
#SECRET=13d41fa9eafac33059fc727a1e6a0706331c9638

log() {
    echo "[$(date +"%Y-%m-%d %H:%M:%S")][INFO] $@" | tee -a ${LOG_OUT}
}

errorlog() {
    echo "[$(date +"%Y-%m-%d %H:%M:%S")][ERROR] $@" | tee -a ${LOG_OUT}
    exit
}

if [ -e logs ] ;then
    log "directory logs"
else 
    if mkdir logs ;then
        log "make directory logs"
    else
        errorlog "make directory logs"
    fi
fi

if [ -e tmp ] ;then
    log "directory tmp"
else 
    if mkdir tmp ;then
        log "make directory tmp"
    else
        errorlog "make directory tmp"
    fi
fi

if [ -e tmp/${TARGET} ] ;then
    if rm -rf tmp/${TARGET} ;then
        log "rm directory ${TARGET}"
    else
        errorlog "rm directory ${TARGET}"
    fi
fi

if [ -e tmp/${ZIP_FILE}.zip ] ;then
    if rm -rf tmp/${ZIP_FILE}.zip ;then
        log "rm zip file"
    else
        errorlog "rm zip file"
    fi
fi

if [ -e tmp/${ZIP_FILE}.zip ] ;then
    if rm -rf tmp/${ZIP_FILE}.zip ;then
        log "rm zip file"
    else
        errorlog "rm zip file"
    fi
fi

#if cp -r ../${TARGET_DIR}/${TARGET} tmp ;then
#   log "cp directory ${TARGET}"
#else
#    errorlog "cp directory ${TARGET}"
#fi
cd ../src/

if git add -N .; git diff --name-only --relative=${TARGET_DIR}/${TARGET}/ | xargs -I % cp --parents ./${TARGET}/% ../.deploy/tmp/ ;then
    cd -
    log "git add -N .; git diff --name-only --relative=${TARGET_DIR}/${TARGET}/ | xargs -I % cp --parents ./${TARGET}/% ../.deploy/tmp/"
else
    cd -
    errorlog "git add -N .; git diff --name-only --relative=${TARGET_DIR}/${TARGET}/ | xargs -I % cp --parents ./${TARGET}/% ../.deploy/tmp/"
fi

cd tmp
if zip -r ${ZIP_FILE} ${TARGET} ;then
    cd -
    log "zip directory ${TARGET}"
else
    cd -
    errorlog "zip directory ${TARGET}"
fi

rm -rf tmp/${TARGET}
if rm -rf tmp/${TARGET} ;then
    log "rm directory ${TARGET}"
else
    errorlog "rm directory ${TARGET}"
fi

cd tmp
if php ../upload.php ${TOKEN} ${SECRET} ${ZIP_FILE}.zip ;then
    cd -
    log "upload ${TARGET}"
else 
    cd -
    errorlog "upload ${TARGET}"
fi

#if java -jar custom_module.jar -t $TOKEN -s $SECRET tmp/upload_data.zip ;then
#    log "upload ${TARGET}"
#else 
#    errorlog "upload ${TARGET}"
#fi

exit;