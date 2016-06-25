#!/bin/sh

list=$( cbsd service mode=list baseonly=1 )

echo "<form action="">"
echo "<div class="field">"

for i in ${list}; do
	echo "<input type="checkbox" class="s_checkbox" name="${i}" value="${i}">${i}<br>"
done

echo "</div>"
echo "</form>"
