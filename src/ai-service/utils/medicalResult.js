// Aucune réponse simulée ou de secours ne devient une donnée médicale.
function normalizeMedicalResult(result) {
    if (!result || result.success !== true || result.mockMode || result.fallback
        || /fallback/i.test(result.note || '')) throw new Error('AI result unavailable');
    let data = result.analysis;
    if (!data && typeof result.text === 'string') {
        const text = result.text.trim().replace(/^\x60\x60\x60(?:json)?\s*/i, '').replace(/\x60\x60\x60\s*$/, '');
        data = JSON.parse(text);
    }
    if (!data || typeof data !== 'object' || Array.isArray(data) || !Object.keys(data).length
        || data.mockMode || data.fallback) throw new Error('Invalid structured AI result');
    return data;
}
module.exports = { normalizeMedicalResult };
