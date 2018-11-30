#!/bin/bash

PWD=$(cd `dirname $0` && pwd)

SCRIPT_DIR="$PWD"

REVIEWERS_IDS="edaka jcampos jrojas gfraser"
echo "$REVIEWERS_IDS" | tr ' ' '\n' | while read -r reviewer; do
  rm -f "$SCRIPT_DIR/$reviewer-pair_review.txt"
done

if [ ! -d "$SCRIPT_DIR/snippets" ]; then
  echo "[ERROR] $SCRIPT_DIR/snippets directory does not exit!"
  exit 1
fi

## Get list of snippets
pushd . > /dev/null 2>&1
cd "$SCRIPT_DIR/snippets"
  ls -la | tail -n +4 > "/tmp/snippets_$$.txt"
  sed -Ei 's/\s+/ /g' "/tmp/snippets_$$.txt"
  cat "/tmp/snippets_$$.txt" | cut -f9 -d' ' > "$SCRIPT_DIR/list_of_all_snippets.txt"
  rm -f "/tmp/snippets_$$.txt"
popd > /dev/null 2>&1

num_snippets=$(wc -l "$SCRIPT_DIR/list_of_all_snippets.txt" | cut -f1 -d' ')
if [ $((a%2)) -eq 1 ]; then
  echo "[ERROR] Number of snippets is not even!"
  exit 1
fi
echo "[DEBUG] num_snippets: $num_snippets"

num_pairs=$((num_snippets/2))
echo "[DEBUG] num_pairs: $num_pairs"

num_reviewers=$(echo "$REVIEWERS_IDS" | tr ' ' '\n' | wc -l)
echo "[DEBUG] num_reviewers: $num_reviewers"

max_num_pairs_per_reviewer=$(echo "($num_pairs/$num_reviewers)+0.5" | bc -l)
max_num_pairs_per_reviewer=$(printf %.0f "$max_num_pairs_per_reviewer")
echo "[DEBUG] max_num_pairs_per_reviewer: $max_num_pairs_per_reviewer"

reviewer=""
reviewer_id=0
num_pairs_of_reviewer=0
seq 1 2 $((num_snippets-1)) | sort -R | while read -r pair_index; do
#    echo "> $pair_index"

  a_snippet_index=$pair_index
  a_snippet_name=$(head -n$a_snippet_index "$SCRIPT_DIR/list_of_all_snippets.txt" | tail -n1)
  b_snippet_index=$((pair_index+1))
  b_snippet_name=$(head -n$b_snippet_index "$SCRIPT_DIR/list_of_all_snippets.txt" | tail -n1)

  if [ "$reviewer" == "" ]; then
    reviewer_id=$((reviewer_id+1))
    reviewer=$(echo "$REVIEWERS_IDS" | tr ' ' '\n' | head -n$reviewer_id | tail -n1)
    echo "[DEBUG] reviewer_id: '$reviewer_id' | reviewer: '$reviewer'"
  fi

  echo "$a_snippet_name,$b_snippet_name" >> "$SCRIPT_DIR/$reviewer-pair_review.txt"
  num_pairs_of_reviewer=$((num_pairs_of_reviewer+1))

  if [ "$num_pairs_of_reviewer" -eq "$max_num_pairs_per_reviewer" ]; then
    # Next reviewer
    reviewer=""
    num_pairs_of_reviewer=0
  fi
done

rm -f "$SCRIPT_DIR/list_of_all_snippets.txt"

# Sanity checks
sanity_num_snippets=$(cat *-pair_review.txt | tr ',' '\n' | sort -u | wc -l)
if [ "$sanity_num_snippets" -ne "$num_snippets" ]; then
  echo "[ERROR] Number of unexpected snippets to review ($sanity_num_snippets vs $num_snippets)!"
  exit 1
fi

sanity_num_pairs=$(cat *-pair_review.txt | sort -u | wc -l)
if [ "$sanity_num_pairs" -ne "$num_pairs" ]; then
  echo "[ERROR] Number of unexpected pairs to review ($sanity_num_pairs vs $num_pairs)!"
  exit 1
fi

echo "DONE!"

# Usefull commands for people to review the pairs:
#$ mkdir /tmp/pairs; cd /tmp/pairs
#$ wget -nv https://github.com/jose/phpmvc/raw/readability_study/public/readability-snippets.tar.bz2 -O readability-snippets.tar.bz2
#$ tar xvjf readability-snippets.tar.bz2
#$ cat __review__file | while read -r pair; do echo ""; echo ""; echo ">>> $pair <<<"; a_snippet=$(echo "$pair" | cut -f1 -d','); b_snippet=$(echo "$pair" | cut -f2 -d','); diff -y -W 200 "snippets/$a_snippet" "snippets/$b_snippet"; done | less

# EOF

