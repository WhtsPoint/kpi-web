const { Telegraf, Markup } = require('telegraf');
const Groq = require('groq-sdk');

const BOT_TOKEN = '7300100568:AAFogBQYe9cdUfxL3WErtURK3nhBR6ZlLo8';

const groqClient = new Groq({
    apiKey: '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'
});

const bot = new Telegraf(BOT_TOKEN);

const mainMenu = Markup.inlineKeyboard([
    [Markup.button.callback('Інформація про студента', 'student_info')],
    [Markup.button.callback('Технології', 'technologies')],
    [Markup.button.callback('Контакти', 'contacts')],
    [Markup.button.callback('Чат з Groq', 'groq_chat')],
]);

let groqChatActive = {};

bot.start((ctx) => {
    groqChatActive[ctx.chat.id] = false; // Вимикаємо режим чату
    ctx.reply('Привіт! Оберіть потрібний пункт:', mainMenu);
});

bot.action('student_info', (ctx) => {
    ctx.editMessageText(
        'Інформація про студента: Полтавський Володимир IO-13',
        Markup.inlineKeyboard([[Markup.button.callback('⬅️ Назад', 'back')]])
    );
});

bot.action('technologies', (ctx) => {
    ctx.editMessageText(
        'Технології:\n- Node.js\n- Groq SDK',
        Markup.inlineKeyboard([[Markup.button.callback('⬅️ Назад', 'back')]])
    );
});

bot.action('contacts', (ctx) => {
    ctx.editMessageText(
        'Контакти:\nТелефон: +38096819****\nЕлектронна пошта: whtspoint@gmail.com',
        Markup.inlineKeyboard([[Markup.button.callback('⬅️ Назад', 'back')]])
    );
});

bot.action('groq_chat', (ctx) => {
    groqChatActive[ctx.chat.id] = true; // Увімкнути режим чату
    ctx.editMessageText(
        'Чекаю на повідомлення (напишіть "стоп", щоб повернутися до меню)',
        Markup.inlineKeyboard([[Markup.button.callback('⬅️ Назад', 'back')]])
    );
});

bot.on('text', async (ctx) => {
    const chatId = ctx.chat.id;

    if (groqChatActive[chatId]) {
        const userMessage = ctx.message.text;

        if (userMessage.toLowerCase() === 'стоп') {
            groqChatActive[chatId] = false;
            return ctx.reply('Привіт! Оберіть потрібний пункт:', mainMenu);
        }

        try {
            const response = await groqClient.chat.completions.create({
                messages: [{ role: 'user', content: userMessage }],
                model: 'llama3-8b-8192'
            });

            const groqResponse = response.choices[0].message.content
            ctx.reply(groqResponse, Markup.inlineKeyboard([[Markup.button.callback('⬅️ Назад', 'back')]]));
        } catch (error) {
            console.error('Помилка при зверненні до Groq API:', error);
            ctx.reply('Сталася помилка під час звернення до Groq. Спробуйте ще раз.');
        }
    }
});

bot.action('back', (ctx) => {
    groqChatActive[ctx.chat.id] = false;
    ctx.editMessageText('Привіт! Оберіть потрібний пункт:', mainMenu);
});

bot.launch()
    .then(() => console.log('Бот запущений!'))
    .catch((err) => console.error('Помилка запуску:', err));

process.on('unhandledRejection', (reason, promise) => {
    console.error('Unhandled Rejection:', reason);
});
process.on('uncaughtException', (err) => {
    console.error('Uncaught Exception:', err);
});