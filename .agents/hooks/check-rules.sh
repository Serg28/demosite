#!/usr/bin/env bash
# PostToolUse хук: після кожного Edit/Write по *.php перевіряє файл на
# механічно-перевірювані пункти .agents/rules/ (не заміняє смисловий рев'ю —
# лише те, що ловиться регуляркою). Блокує (exit 2) з переліком знахідок, щоб
# Claude побачив і вирішив: виправити чи пояснити, чому це не порушення.

set -euo pipefail

input=$(cat)
file_path=$(echo "$input" | jq -r '.tool_input.file_path // empty')

[[ -n "$file_path" ]] || exit 0
[[ "$file_path" == *.php ]] || exit 0
[[ -f "$file_path" ]] || exit 0

# Лінтимо лише код цього репозиторію (не тимчасові tinker-скрипти у scratchpad).
# Корінь виводимо від розташування самого скрипта (.claude/hooks/check-rules.sh),
# щоб хук працював незалежно від абсолютного шляху проекту на конкретній машині.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
[[ "$file_path" == "$PROJECT_ROOT"/* ]] || exit 0
[[ "$file_path" != *"/vendor/"* ]] || exit 0

violations=""

# 1) // коментар довше 3 рядків поспіль (01-baseline.md п.22: ліміт — антибалласт, не форма)
long_comment_lines=$(awk '
  /^[[:space:]]*\/\// { run++; if (run == 4) print NR; next }
  { run=0 }
' "$file_path" | tr '\n' ' ')
if [[ -n "$long_comment_lines" ]]; then
  violations+=$'\n'"- // коментар довше 3 рядків поспіль (з рядка: ${long_comment_lines}) — правило: ліміт 3 рядки, довше — спростити код, а не коментар (перевір: можливо це список закоментованого коду, а не пояснення — тоді не порушення)."
fi

# 1b) "Плаваючий" /** */ докблок, не прив'язаний до оголошення (01-baseline.md п.22)
floating_docblocks=$(awk '
  /^[[:space:]]*\/\*\*/ { in_doc=1 }
  in_doc && /\*\// { in_doc=0; end_line=NR; next }
  end_line && NR==end_line+1 {
    if ($0 !~ /^[[:space:]]*($|#\[|(public|private|protected|abstract|final|static|readonly)[[:space:]]|function[[:space:](]|class[[:space:]]|interface[[:space:]]|trait[[:space:]]|enum[[:space:]]|const[[:space:]]|case[[:space:]])/) {
      print end_line
    }
    end_line=0
  }
' "$file_path" | tr '\n' ' ')
if [[ -n "$floating_docblocks" ]]; then
  violations+=$'\n'"- /** */ докблок не перед оголошенням (рядок завершення: ${floating_docblocks}) — правило: PHPDoc лише перед class/method/property/const, для пояснення в тілі методу — //."
fi

# 2) static-властивість у сервісі/Livewire-компоненті (03-di-lifecycle-octane.md, 05-anti-patterns.md п.8)
if [[ "$file_path" == *"/app/Services/"* || "$file_path" == *"/app/Livewire/"* ]]; then
  statics=$(grep -nE '^[[:space:]]*(private|protected|public)[[:space:]]+static[[:space:]]+\$' "$file_path" || true)
  if [[ -n "$statics" ]]; then
    violations+=$'\n'"- static-властивість (Octane-небезпечно, треба scoped()): $(echo "$statics" | tr '\n' ';')"
  fi
fi

# 3) $request->all()/except() для збереження (05-anti-patterns.md п.1)
mass=$(grep -nE '\$request->(all|except)\(' "$file_path" || true)
if [[ -n "$mass" ]]; then
  violations+=$'\n'"- \$request->all()/except() — тільки validated(): $(echo "$mass" | tr '\n' ';')"
fi

# 4) Sentinel-специфічна перевірка з Shambala тут не застосовна — проєкт на Fortify/Auth-фасаді,
# Auth::user() у Livewire-компонентах є ПРАВИЛЬНИМ патерном (08-livewire-security.md).

if [[ -n "$violations" ]]; then
  echo "Перевірка .agents/rules/ для ${file_path}:${violations}" >&2
  exit 2
fi

exit 0
