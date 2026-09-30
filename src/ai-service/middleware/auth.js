const crypto = require('crypto');
// Authentification du service interne ; aucun secret n'est transmis au navigateur.
module.exports = (req, res, next) => {
    const expected = process.env.AI_API_KEY;
    if (!expected) return res.status(503).json({ success: false, message: 'AI service authentication is not configured' });
    const supplied = (req.headers.authorization || '').replace(/^Bearer\s+/i, '');
    const a = Buffer.from(supplied);
    const b = Buffer.from(expected);
    if (a.length !== b.length || !crypto.timingSafeEqual(a, b)) {
        return res.status(401).json({ success: false, message: 'Unauthorized' });
    }
    next();
};
