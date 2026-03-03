#!/bin/bash
B="http://localhost"
J=~/cookies.txt

curl -s -c $J -H "Origin: $B" "$B/sanctum/csrf-cookie"

XSRF=$(grep XSRF $J | awk '{print $NF}')
DEC=$(python3 -c "import urllib.parse; print(urllib.parse.unquote('$XSRF'))")

D1='{"name":"test",'
D2='"email":"test@example.com",'
D3='"password":"password",'
D4='"password_confirmation":"password"}'
DAT="${D1}${D2}${D3}${D4}"

echo "JSON: $DAT"

curl -s -b $J -c $J \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-H "Origin: $B" \
-H "X-XSRF-TOKEN: $DEC" \
-d "$DAT" \
"$B/api/auth/register"