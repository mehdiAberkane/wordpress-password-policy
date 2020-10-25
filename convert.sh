mkdir ../password-policy-convert
cp -R * ../password-policy-convert
rm ../password-policy-convert/convert.sh
rm -rf ../password-policy-convert/public/.sass-cache
7z.exe a -tzip password-policy.zip ../password-policy-convert
rm -rf ../password-policy-convert
