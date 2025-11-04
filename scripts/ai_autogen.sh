#!/bin/bash
set -e

# =====================================
# 🤖 Shining-Will-Shop AI AutoDev (半無限リトライ版)
# =====================================

REPO_PATH="$HOME/shining-will-shop"
cd "$REPO_PATH"

TIMESTAMP=$(date "+%Y-%m-%d %H:%M:%S")
MAX_RETRIES=10
RETRY_COUNT_FILE="scripts/.ai_retry_count"
RETRY_COUNT=0

if [ -f "$RETRY_COUNT_FILE" ]; then
  RETRY_COUNT=$(cat "$RETRY_COUNT_FILE")
fi

if [ "$RETRY_COUNT" -ge "$MAX_RETRIES" ]; then
  echo "🛑 リトライ上限（$MAX_RETRIES回）に達しました。自動修正を一時停止します。"
  echo "⚠️ 手動で spec.md を見直してください。"
  rm -f "$RETRY_COUNT_FILE"
  exit 1
fi

echo "=== [$TIMESTAMP] 🌙 AI自動生成開始 (試行 ${RETRY_COUNT}/$MAX_RETRIES) ==="

# === 仕様ファイル確認 ===
if [ ! -f "docs/spec.md" ]; then
  echo "❌ docs/spec.md が見つかりません。終了します。"
  exit 1
fi
if [ ! -f ".github/prompts/filament_resource_prompt.md" ]; then
  echo "❌ promptファイルが見つかりません。終了します。"
  exit 1
fi

SPEC_CONTENT=$(cat docs/spec.md)
PROMPT_CONTENT=$(cat .github/prompts/filament_resource_prompt.md)

# === ChatGPT呼び出し ===
echo "=== 🧠 ChatGPT API にリクエスト送信中... ==="
RESPONSE=$(curl https://api.openai.com/v1/chat/completions \
  -s \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $OPENAI_API_KEY" \
  -d "{
    \"model\": \"gpt-5\",
    \"temperature\": 0.4,
    \"messages\": [
      {\"role\": \"system\", \"content\": \"あなたはLaravel + Filamentの上級エンジニアです。\"},
      {\"role\": \"user\", \"content\": \"次の仕様書(spec.md)と開発ルール(prompt.md)をもとにコードを上書き・改善してください。

仕様書:
$SPEC_CONTENT

開発ルール:
$PROMPT_CONTENT\"}
    ]
  }")

OUTPUT_FILE="scripts/ai_output_$(date +%Y%m%d_%H%M%S).md"
echo "$RESPONSE" | jq -r '.choices[0].message.content' > "$OUTPUT_FILE"
echo "✅ ChatGPT出力を保存: $OUTPUT_FILE"

# === コード反映 ===
echo "=== 🧩 ファイル上書きを開始 ==="
awk '/^```/{flag=!flag;next}flag' "$OUTPUT_FILE" > scripts/_temp_code.txt

CURRENT_FILE=""
while IFS= read -r line; do
  if [[ "$line" =~ ^File:\ (.+)$ ]]; then
    CURRENT_FILE="${BASH_REMATCH[1]}"
    echo "📝 書き込み対象: $CURRENT_FILE"
    mkdir -p "$(dirname "$CURRENT_FILE")"
    > "$CURRENT_FILE"
  elif [[ "$CURRENT_FILE" != "" ]]; then
    echo "$line" >> "$CURRENT_FILE"
  fi
done < scripts/_temp_code.txt

echo "✅ ファイル上書き完了"

# === GitHub push ===
git checkout dev
git add .
git commit -m "🤖 AutoGen (try $RETRY_COUNT/$MAX_RETRIES) $TIMESTAMP" || true
git push origin dev
echo "✅ devブランチにpush完了。GitHub Actionsでテストが実行されます。"

# === テスト待機 ===
echo "🕐 テスト結果を待機中（最大10分）..."
sleep 600

# === テスト結果チェック ===
if grep -q "TEST_FAILED" test_status.txt 2>/dev/null; then
  RETRY_COUNT=$((RETRY_COUNT + 1))
  echo "$RETRY_COUNT" > "$RETRY_COUNT_FILE"
  echo "❌ テスト失敗（$RETRY_COUNT/$MAX_RETRIES）→ 再修正を試みます..."
  bash scripts/ai_autogen.sh
else
  echo "✅ テスト成功！リトライカウントをリセット。"
  rm -f "$RETRY_COUNT_FILE"

  # === mainへ自動マージ ===
  echo "🚀 成功！mainブランチへ自動マージをトリガー中..."
  gh workflow run auto-dev-safe.yml
fi
