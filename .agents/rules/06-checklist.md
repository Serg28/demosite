---
paths:
  - "**/*.php"
  - "**/*.blade.php"
---

# Чекліст перед завершенням будь-якої задачі

Фінальний гейт по найризиковіших пунктах baseline (не повторення всіх 29 — решта
застосовується по ходу написання коду, без окремої звірки).

- [ ] Не ламати публічну поведінку без явного запиту (baseline п.1)
- [ ] Імена зрозумілі, без абревіатур-сміття (anti-patterns п.11)
- [ ] Немає зайвої вкладеності/else там, де можна early return
- [ ] Валідація не в контролері
- [ ] Немає нових анти-патернів з `05-anti-patterns.md` (зокрема god-method, приховані побічні ефекти — пп.12-13)
- [ ] Немає N+1 (перевірити `with()` і `whenLoaded()`)
- [ ] Немає вразливостей: XSS, SQL injection, CSRF, mass assignment (baseline п.14)
- [ ] Lifecycle сервісів в контейнері коректний (stateful → `scoped()`, не `singleton()`)
- [ ] Observer-и зареєстровані через `#[ObservedBy]` на моделі, не в AppServiceProvider
- [ ] Коментар/докблок відповідає п.22 baseline: лише WHY (не WHAT); докблок — завершене й самодостатнє речення; формулювання зрозуміле джуну без довідки в іншому файлі; `//` — 1 рядок
- [ ] Помилки не проковтнуті, залоговані з контекстом у свій канал (baseline п.20)
- [ ] Запустити ./vendor/bin/pint

## Короткий режим (token-efficient)
Не заміна фінального гейту вище, а стиснутий прохід по ВСЬОМУ baseline — коли
немає бюджету контексту прочитати всі 29 пунктів окремо:
`типи + DI + FormRequest + no business in Blade + early return + Laravel naming + no abbreviations + no env() outside config + no behavior break + __t/__cms + model->t() + scoped/singleton lifecycle + #[ObservedBy] on model + no N+1 + Octane-safe + Collections/Carbon + comments only non-obvious + docblock = clear standalone sentence`.
