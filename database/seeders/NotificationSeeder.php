<?php

namespace Database\Seeders;

use App\Models\NotificationTemplateLangs;
use App\Models\NotificationTemplates;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $notifications = [
            'new_project'=>'New Project',
            'new_task'=>'New Task',
            'task_stage_updated'=>'Task Stage Updated',
            'new_milestone'=>'New Milestone',
            'milestone_status_updated'=>'Milestone Status Updated',
            'new_task_comment'=>'New Task Comment',
            'new_invoice'=>'New Invoice',
            'invoice_status_updated'=>'Invoice Status Updated',
        ];

        $defaultTemplate = [
                'new_project' => [
                'variables' => '{
                    "Project Name": "project_name",
                    "User Name": "user_name",
                    "App Name": "app_name",
                    "App Url": "app_url"
                    }',
                    'lang' => [
                        'ar' => '{project_name} تم إنشاء المشروع بواسطة {user_name}',
                        'da' => '{project_name} Projekt er oprettet af {user_name}',
                        'de' => '{project_name} Projekt wird erstellt von {user_name}',
                        'en' => '{project_name} Project is Created By {user_name}',
                        'es' => '{project_name} El proyecto se crea mediante {user_name}',
                        'fr' => '{project_name} Le projet est créé par {user_name}',
                        'it' => '{project_name} Il progetto è Creato By {user_name}',
                        'ja' => '{project_name} プロジェクトの作成者 {user_name}',
                        'nl' => '{project_name} Project is gemaakt door {user_name}',
                        'pl' => '{project_name} Projekt został utworzony przez {user_name}',
                        'ru' => '{project_name} Проект создан {user_name}',
                        'pt' => '{project_name} Projeto é Criado Por {user_name}',
                        'tr' => '{ project_name } Projesi, { user_name } Tarafından Oluşturuldu',
                        'zh' => '{project_name} 项目由 {user_name} 创建',
                        'he' => '{project_name} הפרויקט נוצר על ידי {user_name}',
                        'pt-br' => '{project_name} O projeto é criado por {user_name}',
                    ]
            ],

            'new_task' => [
                'variables' => '{
                    "Project Name": "project_name",
                    "Task Title": "task_title",
                    "User Name": "user_name",
                    "App Name": "app_name",
                    "App Url": "app_url"
                    }',
                    'lang' => [
                        'ar' => '{task_title} تم تكوين المهمة بواسطة {user_name} من {project_name} المشروع',
                        'da' => '{task_title} Opgave er oprettet af {user_name} af {project_name} Projekt',
                        'de' => '{task_title} Task wird erstellt von {user_name} von {project_name} Projekt',
                        'en' => '{task_title} Task is Created By {user_name} of {project_name} Project',
                        'es' => '{task_title} La tarea se crea mediante {user_name} de {project_name} Proyecto',
                        'fr' => '{task_title} La tâche est créée par {user_name} De {project_name} Projet',
                        'it' => '{task_title} Attività è creata da {user_name} di {project_name} Progetto',
                        'ja' => '{task_title} タスクの作成者 {user_name} の {project_name} プロジェクト',
                        'nl' => '{task_title} Taak is gemaakt door {user_name} van {project_name} Project',
                        'pl' => '{task_title} Zadanie zostało utworzone przez {user_name} z {project_name} Projekt',
                        'ru' => '{task_title} Задача создана {user_name} из {project_name} Проект',
                        'pt' => '{task_title} Tarefa é Criada Por {user_name} de {project_name} Projeto',
                        'tr' => '{ task_title } Görevi, { user_name } Tarafından { project_name } Projesiyle Oluşturuldu',
                        'zh' => '{task_title } 任务由 {project_name} 项目创建',
                        'he' => '{task_title} משימה נוצרה על ידי {user_name} של פרויקט {project_name}',
                        'pt-br' => '{task_title} A tarefa é criada por {user_name} do projeto {project_name}',
                    ]
            ],

            'task_stage_updated' => [
                'variables' => '{
                    "Project Name": "project_name",
                    "Task Title": "task_title",
                    "User Name": "user_name",
                    "Old Stage": "old_stage",
                    "New Stage": "new_stage",
                    "App Name": "app_name",
                    "App Url": "app_url"
                    }',
                    'lang' => [
                        'ar' => 'مرحلة المهمة من {task_title} تحديث من {old_stage} to {new_stage} ',
                        'da' => 'Opgavetrin for {task_title} opdateret fra {old_stage} to {new_stage} ',
                        'de' => 'Taskstufe von {task_title} Aktualisiert von {old_stage} to {new_stage} ',
                        'en' => 'Task stage of {task_title} updated from {old_stage} to {new_stage}',
                        'es' => 'Fase de tarea de {task_title} actualizado de {old_stage} to {new_stage} ',
                        'fr' => 'Etape de tâche de {task_title} Mis à jour depuis {old_stage} to {new_stage} ',
                        'it' => 'Fase di attività di {task_title} aggiornato da {old_stage} to {new_stage} ',
                        'ja' => 'タスク・ステージ {task_title} 更新元 {old_stage} to {new_stage} ',
                        'nl' => 'Taakstadium van {task_title} bijgewerkt van {old_stage} to {new_stage} ',
                        'pl' => 'Etap zadania {task_title} zaktualizowane od {old_stage} to {new_stage}',
                        'ru' => 'Этап задачи {task_title} обновлено из {old_stage} to {new_stage} Проект',
                        'pt' => 'Estágio de tarefa de {task_title} atualizado de {old_stage} to {new_stage}',
                        'tr' => '{ task_title } görevinin { old_stage } olan görev aşaması { new_stage } olarak güncellendi',
                        'zh' => '{ task_title} 的任务阶段已从 {old_stage} 更新为 {new_stage}',
                        'he' => 'שלב המשימה של {task_title} עודכן מ - {old_השלב} עד {new_stage}',
                        'pt-br' => 'Estágio de tarefa de {task_title} atualizado de {old_stage} para {new_stage}',
                    ]
            ],

            'new_task_comment' => [
                'variables' => '{
                    "Project Name": "project_name",
                    "Task Title": "task_title",
                    "User Name": "user_name",
                    "App Name": "app_name",
                    "App Url": "app_url"
                    }',
                    'lang' => [
                        'ar' => 'تم اضافة التعقيب في {task_title} بواسطة {user_name}',
                        'da' => 'Kommentar tilføjet {task_title} af {user_name}',
                        'de' => 'Kommentar hinzugefügt in {task_title} von {user_name}',
                        'en' => 'Comment Added in {task_title} by {user_name}',
                        'es' => 'Comentario añadido en {task_title} por {user_name}',
                        'fr' => 'Commentaire ajouté dans {task_title} Par {user_name}',
                        'it' => 'Commento Aggiunto in {task_title} di {user_name}',
                        'ja' => '追加されたコメント {task_title} による {user_name}',
                        'nl' => 'Commentaar toegevoegd in {task_title} door {user_name}',
                        'pl' => 'Dodano komentarz do {task_title} przez {user_name}',
                        'ru' => 'Комментарий добавлен в {task_title} по {user_name}',
                        'pt' => 'Comentário Incluído em {task_title} por {user_name}',
                        'tr' => '{ task_title } içinde { user_name } tarafından eklenen yorum',
                        'zh' => '{user_name} 在 {task_title } 中添加了注释',
                        'he' => 'ההערה נוספה בתוך {task_title} על ידי {user_name}',
                        'pt-br' => 'Comentário adicionado em {task_title} por {user_name}',
                    ]
            ],

            'new_milestone' => [
                'variables' => '{
                    "Project Name": "project_name",
                    "Milestone Title": "milestone_title",
                    "User Name": "user_name",
                    "App Name": "app_name",
                    "App Url": "app_url"
                    }',
                    'lang' => [
                        'ar' => '{milestone_title} تم تكوين الحدث الهام بواسطة {user_name} من {project_name} المشروع',
                        'da' => '{milestone_title} Milepæl er oprettet af {user_name} af {project_name} Projekt',
                        'de' => '{milestone_title} Meilenstein wird erstellt von {user_name} von {project_name} Projekt',
                        'en' => '{milestone_title} Milestone is Created By {user_name} of {project_name} Project',
                        'es' => '{milestone_title} El hito se crea por {user_name} de {project_name} Proyecto',
                        'fr' => '{milestone_title} Le jalon est créé par {user_name} De {project_name} Projet',
                        'it' => '{milestone_title} Milestone è Creato By {user_name} di {project_name} Progetto',
                        'ja' => '{milestone_title} マイルストーンの作成者 {user_name} の {project_name} プロジェクト',
                        'nl' => '{milestone_title} Mijlpaal wordt gemaakt door {user_name} van {project_name} Project',
                        'pl' => '{milestone_title} Kamień milowy jest tworzony przez {user_name} z {project_name} Projekt',
                        'ru' => '{milestone_title} Этап создан {user_name} из {project_name} Проект',
                        'pt' => '{milestone_title} O Marco é Criado Por {user_name} de {project_name} Projeto',
                        'tr' => '{milestone_title} Kilometretaşı, { user_name } Tarafından { project_name } Projesiyle Oluşturuldu',
                        'zh' => '{mileestone_title } 里程碑由 {project_name} 项目创建',
                        'he' => '{המייל} אבן דרך נוצר על ידי {user_name} של פרויקט {project_name}',
                        'pt-br' => '{milestone_title} O marco é criado por {user_name} do projeto {project_name}',
                    ]
            ],

            'milestone_status_updated' => [
                'variables' => '{
                    "Project Name": "project_name",
                    "Milestone Title": "milestone_title",
                    "Milestone Status": "milestone_status",
                    "User Name": "user_name",
                    "App Name": "app_name",
                    "App Url": "app_url"
                    }',
                    'lang' => [
                        'ar' => 'بواسطةحالة بالحالة {milestone_title} تحديث بواسطة {user_name}',
                        'da' => 'Milepæl status på {milestone_title} opdateret af {user_name}',
                        'de' => 'Meilenstein Status von {milestone_title} aktualisiert von {user_name}',
                        'en' => 'Milestone status of {milestone_title} updated by {user_name}',
                        'es' => 'El hito se estado de {milestone_title} actualizado por {user_name}',
                        'fr' => 'Le jalon Statut de {milestone_title} Mis à jour par {user_name}',
                        'it' => 'Milestone stato di {milestone_title} aggiornato da {user_name}',
                        'ja' => 'マイルストーン の状況 {milestone_title} 更新者 {user_name}',
                        'nl' => 'Mijlpaal status van {milestone_title} bijgewerkt door {user_name}',
                        'pl' => 'kamień milowy status {milestone_title} zaktualizowane przez {user_name}',
                        'ru' => 'веха состояние {milestone_title} обновлено пользователем {user_name}',
                        'pt' => 'marco status de {milestone_title} atualizado por {user_name}',
                        'tr' => '{ user_name } tarafından güncelleştirilen { mileonone_title } aşama durumu',
                        'zh' => '{user_name} 已更新 {milestone_title} 的里程碑状态',
                        'he' => 'מצב אבן דרך של {המייל stone_title} עודכן על ידי {user_name}',
                        'pt-br' => 'Status do marco de {milestone_title} atualizado por {user_name}',
                    ]
            ],

            'new_invoice' => [
                'variables' => '{
                    "Project Name": "project_name",
                    "Invoice Id": "invoice_id",
                    "Company Name": "company_name",
                    "Client Name": "client_name",
                    "App Name": "app_name",
                    "App Url": "app_url"
                    }',
                    'lang' => [
                        'ar' => 'فاتورة جديدة {invoice_id} تكوين بواسطة {company_name}',
                        'da' => 'Ny faktura {invoice_id} oprettet af {company_name}',
                        'de' => 'Neue Rechnung {invoice_id} erstellt von {company_name}',
                        'en' => 'New Invoice {invoice_id} created by {company_name}',
                        'es' => 'Nueva factura {invoice_id} creado por {company_name}',
                        'fr' => 'Nouvelle facture {invoice_id} Créé par {company_name}',
                        'it' => 'Nuova Fattura {invoice_id} creato da {company_name}',
                        'ja' => '新規請求書 {invoice_id} 作成者 {company_name}',
                        'nl' => 'Nieuwe factuur {invoice_id} gemaakt door {company_name}',
                        'pl' => 'Nowa faktura {invoice_id} utworzone przez {company_name}',
                        'ru' => 'Новая накладная {invoice_id} кем создано {company_name}',
                        'pt' => 'Nova Fatura {invoice_id} criado por {company_name}',
                        'tr' => '{ company_name } tarafından oluşturulan yeni Fatura { invoice_id }',
                        'zh' => '{company_name} 创建的新发票 {invoice_id}',
                        'he' => 'חשבונית חדשה {invoice_id} נוצרה על-ידי {company_name}',
                        'pt-br' => 'Nova fatura {invoice_id} criada por {company_name}',
                    ]
            ],

            'invoice_status_updated' => [
                'variables' => '{
                    "Project Name": "project_name",
                    "Invoice Id": "invoice_id",
                    "Invoice Status": "invoice_status",
                    "Company Name": "company_name",
                    "Client Name": "client_name",
                    "App Name": "app_name",
                    "App Url": "app_url",
                    "Paid Amount": "paid_amount",
                    "Total Amount": "total_amount"
                    }',
                    'lang' => [
                        'ar' => '{invoice_id}: تم دفع {pay_amount} بنجاح بواسطة {client_name} المبلغ الإجمالي: {total_amount}',
                        'da' => '{invoice_id}: Betalt {paid_amount} med succes af {client_name} Samlet beløb: {total_amount}',
                        'de' => '{invoice_id}: {paid_amount} erfolgreich bezahlt von {client_name} Gesamtbetrag: {total_amount}',
                        'en' => '{invoice_id}:  Paid {paid_amount} Successfully By {client_name}  Total amount :  {total_amount}',
                        'es' => '{invoice_id}: {paid_amount} pagado con éxito por {client_name} Monto total: {total_amount}',
                        'fr' => '{invoice_id}: Payé {paid_amount} avec succès par {client_name} Montant total: {total_amount}',
                        'it' => '{invoice_id}: {paid_amount} pagato con successo da {client_name} Importo totale: {total_amount}',
                        'ja' => '{invoice_id}: {client_name} によって {paid_amount} が正常に支払われました合計金額: {total_amount}',
                        'nl' => '{invoice_id}: {paid_amount} succesvol betaald door {client_name} Totaalbedrag: {total_amount}',
                        'pl' => '{invoice_id}: Pomyślnie zapłacono {paid_amount} przez {client_name} Łączna kwota: {total_amount}',
                        'ru' => '{invoice_id}: {paid_amount} успешно выплачен {client_name} Общая сумма: {total_amount}',
                        'pt' => '{invoice_id}: Pago {paid_amount} com sucesso por {client_name} Valor total: {total_amount}',
                        'tr' => '{ invoice_id }: Paid { paid_amount } Başarıyla { client_name } Toplam Tutarı Ile Başarıyla: { total_amount }',
                        'zh' => '{invoice_id}: 已成功 { paid_金额} 按 {client_name} 总金额 : {total_金额}',
                        'he' => '{invoice_id}: שולם {paid_סכום} בהצלחה על-ידי {client_name} סכום כולל: {total_בסכום}',
                        'pt-br' => '{invoice_id}: Pago {paid_amount} com êxito por {client_name} Valor total : {total_amount}',
                    ]
            ],

      ];


        $user = User::where('type','admin')->first();

        foreach($notifications as $k => $n)
        {
            $ntfy = NotificationTemplates::where('slug',$k)->count();
            if($ntfy == 0)
            {
                $new = new NotificationTemplates();
                $new->name = $n;
                $new->slug = $k;
                $new->save();

                foreach($defaultTemplate[$k]['lang'] as $lang => $content)
                {
                    NotificationTemplateLangs::create(
                        [
                            'parent_id' => $new->id,
                            'lang' => $lang,
                            'variables' => $defaultTemplate[$k]['variables'],
                            'content' => $content,
                            'created_by' => !empty($user) ? $user->id : 1,
                        ]
                    );
                }
            }
        }
    }
}
