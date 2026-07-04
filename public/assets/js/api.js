window.AtendeLabApi = (() => {
    // Se o 404 persistir, tente ajustar o baseUrl para o caminho real da sua pasta
    const baseUrl = '/atendelab/public/index.php'; 

    async function request(controller, action, { method = 'GET', query = {}, body = null } = {}) {
        // Constrói a URL com os parâmetros de controller e action
        const url = new URL(window.location.origin + baseUrl);
        url.searchParams.append('controller', controller);
        url.searchParams.append('action', action);
        
        // Adiciona queries extras se houver
        Object.entries(query).forEach(([key, value]) => {
        url.searchParams.append(key, value);
    });

        const options = { method, credentials: 'same-origin' };

        if (method !== 'GET' && body !== null) {
            const form = body instanceof FormData ? body : objectToFormData(body);
            // Corrige o envio do body para garantir compatibilidade com o backend PHP
            options.body = new URLSearchParams([...form.entries()]);
            options.headers = { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' };
        }

        const response = await fetch(url.toString(), options);
        
        // Verifica se a página retornou algo (texto ou JSON)
        const text = await response.text();
        let data;

        try {
            data = text ? JSON.parse(text) : {};
        } catch (e) {
            console.error("Erro ao converter JSON:", text);
            throw new Error('Erro de comunicação com o servidor.');
        }

        if (!response.ok || (data && data.erro)) {
            throw new Error(data.erro || data.mensagem || `Erro HTTP ${response.status}`);
        }
        return data;
    }

    function objectToFormData(obj) {
        const form = new FormData();
        for (const [key, value] of Object.entries(obj)) {
            if (value !== null && value !== undefined) {
                form.append(key, String(value));
            }
        }
        return form;
    }

    // ... (restante das funções toList, toObject, escape, showAlert permanecem iguais)
    function toList(data) { if (Array.isArray(data)) return data; return []; }
    function toObject(data) { if (!data || typeof data !== 'object') return {}; return data; }
    function escape(value) { return String(value ?? '').replace(/[&<>'"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[c])); }
    function showAlert(id, message, type = 'success') {
        const element = document.getElementById(id);
        if (!element) return;
        element.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show">${escape(message)}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
    }

    return {
        get: (controller, action, query = {}) => request(controller, action, { query }),
        post: (controller, action, body = {}) => request(controller, action, { method: 'POST', body }),
        toList, toObject, escape, showAlert
    };
})();