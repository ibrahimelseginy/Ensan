# تصميم قاعدة بيانات الحسابات V2

## علاقات البيانات

```text
financial_accounts ──< ledger_entry_lines >── ledger_entries >── accounting_periods
       ▲                         │                    │
cash_accounts ───────────────────┘                    └── financial_documents

funds ──< fund_balances ──< fund_movements >── ledger_entries

cash_accounts ──< cash_transfers >── cash_accounts
custodies ──< custody_expenses
cash_accounts ──< bank_reconciliations ──< bank_reconciliation_lines
```

## الجداول المنفذة

| الجدول | الغرض | قيود السلامة المهمة |
|---|---|---|
| `financial_accounts` | شجرة الحسابات الجديدة | `code` فريد، حساب تجميعي غير قابل للترحيل عبر `is_posting=false` |
| `accounting_periods` | فترات محاسبية | لا يرحّل محرك القيود إلا في `open` |
| `funds` | صندوق حر/مقيد/زكاة/كفالة/حملة | سياسة صرف ومنع سالب مستقلان |
| `fund_balances` | projection مقفل للصندوق | `(fund_id,currency)` فريد و`lock_version` للتزامن |
| `cash_accounts` | موقع مال فعلي | حساب GL واحد لكل موقع، ولا علاقة مشروع/حملة |
| `cash_account_users` | ACL للحساب النقدي | مستخدم/حساب فريد وخمس صلاحيات دقيقة |
| `financial_documents` | سندات مرقمة | رقم مستند فريد وإبطال بسبب ومستخدم |
| `ledger_entries` | رأس القيد المرحل | رقم ومفتاح idempotency فريدان وعكس مرتبط بالأصل |
| `ledger_entry_lines` | أطراف القيد وأبعاده | ترتيب سطر فريد ومدين/دائن وأبعاد تحليليّة |
| `fund_movements` | سجل تخصيص/استهلاك/حجز الصندوق | مربوط بصف الرصيد المقفل وبالقيد إن وجد |
| `donation_allocations` | سطور تخصيص التبرع المطبّعة | ترتيب فريد لكل تبرع ومجموع يفرضه محرك الخدمة |
| `cash_transfers` | طلب وتسلم التحويل الداخلي | مصدر ومستلم وقيد واحد عند الاستلام |
| `cash_transfer_items` | الإيصالات/الحركات الداخلة في التوريد | رابط مصدر واضح ودعم التوريد الجزئي |
| `payment_instruments` | شيك أو تحويل بنك/محفظة | حالات draft/issued/cleared/failed/voided/returned بلا خصم نهائي قبل التأكيد |
| `custodies` | عهدة الموظف | مبلغ صادر/مسوّى/مرتجع وحالة وتاريخ استحقاق |
| `custody_expenses` | فواتير تسوية العهد | فاتورة فريدة ضمن العهدة وقيد اعتماد مستقل |
| `bank_reconciliations` | رأس دورة المطابقة | فترة كشف فريدة لكل حساب بنك |
| `bank_reconciliation_lines` | سطور كشف البنك ومطابقتها | حالة unmatched/matched وفهرس للمراجعة |
| `financial_approvals` | قرار كل مرحلة اعتماد | مرحلة واحدة فريدة لكل موضوع وقرار موثق |

## قواعد بيانات ومحرك إلزامية

التحقق من توازن القيد وقاعدة XOR للمدين/الدائن ينفذان داخل `LedgerPostingService` في transaction واحدة، لأن MySQL لا يضمن توازن عدة صفوف عبر CHECK عادي. قبل الإطلاق يوصى بإضافة trigger دفاعي على بيئة MySQL المدعومة بعد اختبار البنية.

```text
line.debit > 0 XOR line.credit > 0
SUM(entry.debit) = SUM(entry.credit)
posted entry => append-only
one reversal per original entry
period.status = open
cash transfer posts only after receiver confirmation
fund.available >= consumption unless exception policy allows it
```

## خرائط التحويل من V1

| V1 | V2 | قاعدة النقل |
|---|---|---|
| `treasuries` ذات النقد/بنك/محفظة | `cash_accounts` + حساب أصل في `financial_accounts` | خريطة واحدة مع جرد فعلي |
| `treasuries` من نوع project/campaign | `funds` وأبعاد سطور القيد | لا تنقل رصيدها كنقد |
| `treasury_transactions` | `ledger_entries/lines` مرجعية | لا تعديل أصل؛ يرصد الاستثناء غير المتزن |
| `donations.split_allocations` | سطور تخصيص normalized | تحقق أن المجموع يساوي المبلغ قبل الترحيل |
| `expenses` | حدث صرف وسطور توزيع | تفكيك المصروف متعدد المشروع إلى سطور |
| خزينة مندوب | `custodies` | الرصيد الافتتاحي ذمة، لا مصروف |

## الفهارس والأمان

- تستخدم مفاتيح خارجية `RESTRICT` في سجلات المال كي لا يمحو حذف حساب أو مستخدم التاريخ.
- تستخدم المبالغ `DECIMAL(19,4)`، وليس float، في التخزين.
- تستخدم معرفات `source_type/source_id` فقط للربط التشغيلي؛ لا تغني عن مرجع القيد أو الوثيقة.
- لا يخزن رقم IBAN كاملاً في هذا المخطط؛ يضاف في وحدة المستفيدين مع تشفير على مستوى التطبيق وسجل وصول.
