rem @echo off

set /p version=Enter version: 
set filename=fusionnews-%version%.zip

if exist %filename% del %filename%

set cwd=%cd%

cd ..
echo Packaging files...

if exist upload rmdir /S /Q upload
mkdir upload

xcopy *.php upload\
copy *.php.default upload\*.
xcopy jsfunc.js upload\
xcopy /E ckeditor\* upload\ckeditor\
rmdir /S /Q upload\ckeditor\_source
rmdir /S /Q upload\ckeditor\_samples
rmdir /S /Q upload\ckeditor\adapters
del /Q upload\ckeditor\*.html upload\ckeditor\*_source.* upload\ckeditor\*.asp upload\ckeditor\*.php upload\ckeditor\ckeditor.pack upload\ckeditor\ckpackager.jar
xcopy img\* upload\img\
xcopy news\*.html upload\news\
copy news\toc.php.default upload\news\toc.php
xcopy /E news\fonts\* upload\news\fonts\
xcopy /E skins\* upload\skins\
xcopy /E smillies\* upload\smillies\
xcopy /E templates\* upload\templates\
xcopy uploads\index.html upload\uploads\

zip -r dev\%filename% docs\*
zip -r dev\%filename% upload\*

rmdir /S /Q upload

cd %cwd%

pause
