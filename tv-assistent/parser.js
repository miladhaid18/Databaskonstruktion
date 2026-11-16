(function (root, factory) {
    if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        root.TVParser = factory();
    }
})(typeof self !== 'undefined' ? self : this, function () {
    'use strict';

    const CHANNELS = [
        { slug: 'svt1', name: 'SVT1', aliases: ['svt1', 'svt 1', 'svt ett', 'ettan', 'kanal 1'] },
        { slug: 'svt2', name: 'SVT2', aliases: ['svt2', 'svt 2', 'svt två', 'tvåan', 'kanal 2'] },
        { slug: 'tv3', name: 'TV3', aliases: ['tv3', 'tv 3', 'trean', 'kanal 3'] },
        { slug: 'tv4', name: 'TV4', aliases: ['tv4', 'tv 4', 'fyran', 'kanal 4'] },
        { slug: 'kanal-5', name: 'Kanal 5', aliases: ['kanal5', 'kanal 5', 'femman', 'tv5', 'tv 5'] },
        { slug: 'tv6', name: 'TV6', aliases: ['tv6', 'tv 6', 'sexan', 'kanal 6'] },
        { slug: 'sjuan', name: 'Sjuan', aliases: ['sjuan', 'kanal 7', 'kanal7', 'tv7', 'tv 7'] },
        { slug: 'tv8', name: 'TV8', aliases: ['tv8', 'tv 8', 'åttan', 'kanal 8'] },
        { slug: 'kanal-9', name: 'Kanal 9', aliases: ['kanal9', 'kanal 9', 'nian', 'tv9', 'tv 9'] },
        { slug: 'tv10', name: 'TV10', aliases: ['tv10', 'tv 10', 'tian', 'kanal 10'] },
        { slug: 'kanal-11', name: 'Kanal 11', aliases: ['kanal11', 'kanal 11', 'elvan', 'tv11', 'tv 11'] },
        { slug: 'tv12', name: 'TV12', aliases: ['tv12', 'tv 12', 'tolvan', 'kanal 12'] },
        { slug: 'viaplay-sport', name: 'Viaplay Sport', aliases: ['viaplay sport', 'viaplaysport', 'viaplay'] },
        { slug: 'eurosport-1', name: 'Eurosport 1', aliases: ['eurosport 1', 'eurosport1', 'eurosport'] },
        { slug: 'kunskapskanalen', name: 'Kunskapskanalen', aliases: ['kunskapskanalen', 'kunskaps kanalen'] },
        { slug: 'svt24', name: 'SVT24', aliases: ['svt24', 'svt 24'] },
        { slug: 'svt-barn', name: 'SVT Barn', aliases: ['svt barn', 'svtbarn', 'barnkanalen', 'barn kanalen'] },
        { slug: 'tlc', name: 'TLC', aliases: ['tlc'] },
        { slug: 'koket', name: 'Köket', aliases: ['köket', 'koket', 'kökskanalen'] }
    ];

    const NUMBER_WORDS = {
        noll: 0, ett: 1, en: 1, två: 2, tre: 3, fyra: 4, fem: 5, sex: 6,
        sju: 7, åtta: 8, nio: 9, tio: 10, elva: 11, tolv: 12, tretton: 13,
        fjorton: 14, femton: 15, sexton: 16, sjutton: 17, arton: 18,
        nitton: 19, tjugo: 20
    };

    const COMPOUND_NUMBERS = {
        tjugoett: 21, tjugoen: 21, tjugotvå: 22, tjugotre: 23, tjugofyra: 24,
        tjugofem: 25, tjugosex: 26, tjugosju: 27, tjugoåtta: 28, tjugonio: 29
    };

    const MONTHS = [
        'januari', 'februari', 'mars', 'april', 'maj', 'juni',
        'juli', 'augusti', 'september', 'oktober', 'november', 'december'
    ];

    const MONTH_INDEX = {
        januari: 0, februari: 1, mars: 2, april: 3, maj: 4, juni: 5,
        juli: 6, augusti: 7, september: 8, oktober: 9, november: 10, december: 11
    };

    const WEEKDAY_INDEX = {
        söndag: 0, måndag: 1, tisdag: 2, onsdag: 3, torsdag: 4, fredag: 5, lördag: 6
    };

    const LEFT = '(?<![\\p{L}\\p{N}])';
    const RIGHT = '(?![\\p{L}\\p{N}])';

    function wordRe(inner, flags) {
        return new RegExp(LEFT + inner + RIGHT, flags || 'iu');
    }

    function pad2(value) {
        return String(value).padStart(2, '0');
    }

    function formatDate(date) {
        return date.getFullYear() + '-' + pad2(date.getMonth() + 1) + '-' + pad2(date.getDate());
    }

    function parseDate(value) {
        const parts = value.split('-').map(Number);
        return new Date(parts[0], parts[1] - 1, parts[2]);
    }

    function startOfDay(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    function addDays(date, days) {
        const next = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        next.setDate(next.getDate() + days);
        return next;
    }

    function stripGreeting(text) {
        let result = text.trim();
        result = result.replace(/^(?:hej|hejsan|tjena|tjenare|hallå|halla|god\s+morgon|god\s+kväll)[\s,!.]*/iu, '');
        result = result.replace(/^(?:thomas|tomas)[\s,!.]*/iu, '');
        return result.trim();
    }

    function wordsToNumbers(text) {
        let output = text.replace(
            wordRe('(tjugoett|tjugoen|tjugotvå|tjugotre|tjugofyra|tjugofem|tjugosex|tjugosju|tjugoåtta|tjugonio)', 'giu'),
            function (match) {
                const value = COMPOUND_NUMBERS[match.toLowerCase()];
                return value === undefined ? match : String(value);
            }
        );

        output = output.split(/(\s+)/).map(function (token) {
            if (/^\s+$/.test(token)) {
                return token;
            }

            const cleaned = token.replace(/^[^\p{L}\p{N}]+|[^\p{L}\p{N}]+$/gu, '');
            if (cleaned === '') {
                return token;
            }

            const key = cleaned.toLowerCase();
            if (!Object.prototype.hasOwnProperty.call(NUMBER_WORDS, key)) {
                return token;
            }

            const start = token.indexOf(cleaned);
            const before = token.slice(0, start);
            const after = token.slice(start + cleaned.length);
            return before + NUMBER_WORDS[key] + after;
        }).join('');

        return output;
    }

    function extractDate(input, now) {
        let rest = ' ' + input + ' ';
        const today = startOfDay(now);
        let selected = null;
        let explicit = false;
        let wantsEvening = false;
        let wantsNight = false;

        function consume(re) {
            const match = rest.match(re);
            if (!match) {
                return null;
            }
            rest = rest.replace(re, ' ');
            return match;
        }

        let match = consume(wordRe('(20\\d{2})[-/](\\d{1,2})[-/](\\d{1,2})'));
        if (match) {
            const candidate = new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]));
            if (!Number.isNaN(candidate.getTime())) {
                selected = candidate;
                explicit = true;
            }
        }

        if (selected === null) {
            match = consume(wordRe('(?:den\\s+)?(\\d{1,2})(?:\\s+|:)(januari|februari|mars|april|maj|juni|juli|augusti|september|oktober|november|december)'));
            if (match) {
                const day = Number(match[1]);
                const month = MONTH_INDEX[match[2].toLowerCase()];
                if (month !== undefined && day >= 1 && day <= 31) {
                    let candidate = new Date(today.getFullYear(), month, day);
                    if (candidate < today) {
                        candidate = new Date(today.getFullYear() + 1, month, day);
                    }
                    selected = candidate;
                    explicit = true;
                }
            }
        }

        if (selected === null) {
            match = consume(wordRe('den\\s+(\\d{1,2})[\\/.-](\\d{1,2})'));
            if (match) {
                const day = Number(match[1]);
                const month = Number(match[2]) - 1;
                if (day >= 1 && day <= 31 && month >= 0 && month <= 11) {
                    let candidate = new Date(today.getFullYear(), month, day);
                    if (candidate < today) {
                        candidate = new Date(today.getFullYear() + 1, month, day);
                    }
                    selected = candidate;
                    explicit = true;
                }
            }
        }

        if (selected === null) {
            if (/(?:i\s*)?övermorgon/iu.test(rest)) {
                selected = addDays(today, 2);
                explicit = true;
                rest = rest.replace(/(?:i\s*)?övermorgon/giu, ' ');
            } else if (/(?:i\s*)?förrgår/iu.test(rest)) {
                selected = addDays(today, -2);
                explicit = true;
                rest = rest.replace(/(?:i\s*)?förrgår/giu, ' ');
            } else if (/i\s*morgon|imorgon/iu.test(rest)) {
                selected = addDays(today, 1);
                explicit = true;
                rest = rest.replace(/i\s*morgon|imorgon/giu, ' ');
            } else if (/i\s*går|igår/iu.test(rest)) {
                selected = addDays(today, -1);
                explicit = true;
                rest = rest.replace(/i\s*går|igår/giu, ' ');
            } else if (/i\s*dag|idag/iu.test(rest)) {
                selected = today;
                explicit = true;
                rest = rest.replace(/i\s*dag|idag/giu, ' ');
            }
        }

        if (selected === null) {
            match = consume(wordRe('(?:på\\s+|nästa\\s+)?(söndag|måndag|tisdag|onsdag|torsdag|fredag|lördag)'));
            if (match) {
                let different = (WEEKDAY_INDEX[match[1].toLowerCase()] - today.getDay() + 7) % 7;
                if (/nästa/i.test(match[0]) && different === 0) {
                    different = 7;
                }
                selected = addDays(today, different);
                explicit = true;
            }
        }

        if (/(?:i\s*kväll|ikväll)|kväll(?:en)?/iu.test(rest)) {
            wantsEvening = true;
            explicit = true;
            rest = rest.replace(/(?:i\s*kväll|ikväll)/giu, ' ');
            rest = rest.replace(wordRe('kväll(?:en)?', 'giu'), ' ');
        }

        if (/(?:i\s*natt|inatt)|natt(?:en)?/iu.test(rest)) {
            wantsNight = true;
            explicit = true;
            rest = rest.replace(/(?:i\s*natt|inatt)/giu, ' ');
            rest = rest.replace(wordRe('natt(?:en)?', 'giu'), ' ');
        }

        if (selected === null) {
            selected = today;
        }

        return {
            date: formatDate(selected),
            explicit: explicit,
            rest: rest,
            wantsEvening: wantsEvening,
            wantsNight: wantsNight
        };
    }

    function escapeRegExp(value) {
        return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function aliasPattern(alias) {
        const parts = alias.split(/\s+/).map(escapeRegExp);
        return new RegExp(LEFT + parts.join('\\s+') + RIGHT, 'iu');
    }

    function extractChannels(input) {
        let rest = input;
        const found = [];
        const allPattern = wordRe('alla\\s+kanaler|samtliga\\s+kanaler|alla\\s+tv-?kanaler');

        const allChannels = allPattern.test(rest);
        if (allChannels) {
            rest = rest.replace(wordRe('alla\\s+kanaler|samtliga\\s+kanaler|alla\\s+tv-?kanaler', 'giu'), ' ');
        }

        CHANNELS.forEach(function (channel) {
            let matched = false;
            channel.aliases.slice().sort(function (a, b) {
                return b.length - a.length;
            }).forEach(function (alias) {
                const pattern = aliasPattern(alias);
                if (pattern.test(rest)) {
                    matched = true;
                    rest = rest.replace(aliasPattern(alias), ' ');
                }
            });

            if (matched) {
                found.push({ slug: channel.slug, name: channel.name });
            }
        });

        return { channels: found, rest: rest, allChannels: allChannels };
    }

    function finalizeTime(hour, minute, options) {
        if (!Number.isFinite(hour) || !Number.isFinite(minute)) {
            return null;
        }

        while (minute >= 60) {
            minute -= 60;
            hour += 1;
        }
        while (minute < 0) {
            minute += 60;
            hour -= 1;
        }

        const eveningContext = options.wantsEvening || options.wantsNight;
        if (eveningContext && hour === 12) {
            hour = 0;
        } else if (options.wantsEvening && hour >= 6 && hour <= 11) {
            hour += 12;
        }

        hour = ((hour % 24) + 24) % 24;
        return pad2(hour) + ':' + pad2(minute);
    }

    function extractTime(input, options, originalText) {
        let rest = ' ' + input + ' ';
        let time = null;
        let timeWasNow = false;

        const nowPattern = wordRe('just\\s+nu|nu');
        if (nowPattern.test(rest)) {
            timeWasNow = true;
            rest = rest.replace(wordRe('just\\s+nu|nu', 'giu'), ' ');
        }

        function tryMatch(pattern, handler) {
            if (time) {
                return;
            }
            const match = rest.match(pattern);
            if (!match) {
                return;
            }
            const value = handler(match);
            if (value) {
                time = value;
                rest = rest.replace(pattern, ' ');
            }
        }

        tryMatch(wordRe('(?:klockan\\s+)?(kvart\\s+(?:i|över|efter))\\s+(\\d{1,2})'), function (match) {
            const base = Number(match[2]);
            const before = /^kvart\s+i/iu.test(match[1]);
            return finalizeTime(before ? base - 1 : base, before ? 45 : 15, options);
        });

        tryMatch(wordRe('(?:klockan\\s+)?halv\\s+(\\d{1,2})'), function (match) {
            return finalizeTime(Number(match[1]) - 1, 30, options);
        });

        tryMatch(wordRe('(fem|tio|tjugo)\\s+(över|i)\\s+(\\d{1,2})'), function (match) {
            const minutes = Number(NUMBER_WORDS[match[1].toLowerCase()]);
            const base = Number(match[3]);
            if (match[2].toLowerCase() === 'i') {
                return finalizeTime(base - 1, 60 - minutes, options);
            }
            return finalizeTime(base, minutes, options);
        });

        tryMatch(wordRe('(?:klockan\\s+|kl\\.?\\s+)?(\\d{1,2})[:.](\\d{2})'), function (match) {
            return finalizeTime(Number(match[1]), Number(match[2]), options);
        });

        tryMatch(wordRe('(?:klockan|kl\\.?)\\s*(\\d{1,2})'), function (match) {
            return finalizeTime(Number(match[1]), 0, options);
        });

        tryMatch(wordRe('(?:vid|runt)\\s+(\\d{1,2})'), function (match) {
            return finalizeTime(Number(match[1]), 0, options);
        });

        if (!time && originalText) {
            const bare = originalText.match(wordRe('(\\d{1,2})\\s*(?:i\\s*kväll|ikväll|i\\s*morgon|imorgon|idag|i\\s*natt|inatt)'));
            if (bare) {
                time = finalizeTime(Number(bare[1]), 0, options);
                timeWasNow = false;
            }
        }

        if (time) {
            timeWasNow = false;
        }

        return { time: time, timeWasNow: timeWasNow, rest: rest };
    }

    function detectSearch(text) {
        const patterns = [
            /^(?:när|vilken\s+tid)\s+(?:börjar|startar|sänds|går|visas|kommer)\s+(.+)$/iu,
            /^(?:när|vilken\s+tid)\s+(.+?)\s+(?:börjar|startar|sänds|går|visas)$/iu,
            /^(?:sök(?:a)?(?:\s+efter)?|hitta|leta\s+efter)\s+(.+)$/iu,
            /^vilken\s+kanal\s+(?:går|sänds|visas)\s+(.+)$/iu,
            /^var\s+(?:går|sänds|visas)\s+(.+)$/iu
        ];

        for (let index = 0; index < patterns.length; index += 1) {
            const match = text.match(patterns[index]);
            if (match && match[1]) {
                return match[1].trim();
            }
        }

        return null;
    }

    function cleanSearchTerm(term) {
        let output = ' ' + term + ' ';
        output = output.replace(/(?:klockan|kl)\b/giu, ' ');
        output = output.replace(/[?!.,;:]+/gu, ' ');
        output = output.replace(/\s+/gu, ' ').trim();

        let previous;
        do {
            previous = output;
            output = output.replace(/\s+(?:på|i|vid|runt)$/iu, '').trim();
        } while (output !== previous);

        return output;
    }

    function hasQuestionSignal(text) {
        return /vad|vilket|vilka|program|går|sänder|sänds|visas|kanal|tv|klockan|kl\b|ikväll|imorgon|idag|igår|nu/iu.test(text);
    }

    function parseQuery(raw, context, now) {
        const current = now instanceof Date ? now : new Date();
        const original = String(raw || '').trim();
        const withoutGreeting = stripGreeting(original);

        if (withoutGreeting === '') {
            return { type: 'greet', raw: original };
        }

        const lower = withoutGreeting.toLowerCase();

        if (/^(?:stopp|tyst|var\s+tyst|avbryt|avsluta)[.!\s]*$/iu.test(lower)) {
            return { type: 'stop', raw: original };
        }

        if (/^(?:tack|tusen\s+tack|tack\s+så\s+mycket)[.!\s]*$/iu.test(lower)) {
            return { type: 'thanks', raw: original };
        }

        if (/^(?:hjälp|hjalp|vad\s+kan\s+du|vad\s+kan\s+du\s+göra|hur\s+funkar(?:\s+det)?|hur\s+fungerar(?:\s+det)?)[.?!\s]*$/iu.test(lower)) {
            return { type: 'help', raw: original };
        }

        if (/^(?:vilka|lista|visa)\s+(?:alla\s+)?(?:tv-?)?kanaler(?:\s+(?:har|finns)\s+du)?(?:\s+finns(?:\s+det)?)?[.?!\s]*$/iu.test(lower)) {
            return { type: 'channels', raw: original };
        }

        const normalized = wordsToNumbers(lower);
        const searchTermRaw = detectSearch(normalized);

        if (searchTermRaw) {
            const searchDate = extractDate(searchTermRaw, current);
            const searchChannels = extractChannels(searchDate.rest);
            const searchTime = extractTime(
                searchChannels.rest,
                { wantsEvening: searchDate.wantsEvening, wantsNight: searchDate.wantsNight },
                normalized
            );
            const term = cleanSearchTerm(searchTime.rest);

            if (term.length >= 2) {
                return {
                    type: 'search',
                    raw: original,
                    searchTerm: term,
                    channels: searchChannels.channels,
                    allChannels: searchChannels.allChannels,
                    date: searchDate.date,
                    dateExplicit: searchDate.explicit,
                    time: searchTime.time,
                    timeWasNow: searchTime.timeWasNow
                };
            }
        }

        const dateInfo = extractDate(normalized, current);
        const channelInfo = extractChannels(dateInfo.rest);
        const timeInfo = extractTime(
            channelInfo.rest,
            { wantsEvening: dateInfo.wantsEvening, wantsNight: dateInfo.wantsNight },
            normalized
        );

        let channels = channelInfo.channels.slice();
        let allChannels = channelInfo.allChannels;
        let time = timeInfo.time;
        let timeIsNow = timeInfo.timeWasNow;
        let date = dateInfo.date;
        let dateExplicit = dateInfo.explicit;

        const wordCount = normalized.split(/\s+/).filter(Boolean).length;
        const followUpPrefix = /^(?:och|då|samma|där|på\s+samma|hur\s+är\s+det\s+med)\b/iu.test(normalized);
        const shortWithTime = channels.length === 0 && Boolean(time) && !dateExplicit && wordCount <= 6;
        const dateOnlyFollowUp = channels.length === 0 && !time && dateExplicit
            && /^(?:och\s+)?(?:imorgon|idag|ikväll|i\s+morgon|i\s+kväll)(?:\s+då)?[.?!\s]*$/iu.test(normalized);
        const isFollowUp = followUpPrefix || shortWithTime || dateOnlyFollowUp;

        if (isFollowUp && context) {
            if (channels.length === 0 && !allChannels && Array.isArray(context.channels) && context.channels.length > 0) {
                channels = context.channels.map(function (channel) {
                    return { slug: channel.slug, name: channel.name };
                });
            }
            if (!time && !timeIsNow && context.time) {
                if (context.time === 'now') {
                    timeIsNow = true;
                } else {
                    time = context.time;
                }
            }
            if (!dateExplicit && context.date) {
                date = context.date;
            }
        }

        if (timeIsNow) {
            time = 'now';
        } else if (!time) {
            if (channels.length > 0) {
                time = null;
            } else if (dateInfo.wantsEvening) {
                time = '20:00';
            } else if (date !== formatDate(startOfDay(current))) {
                time = '20:00';
            } else {
                time = 'now';
            }
        }

        if (time && time !== 'now') {
            const hour = Number(time.slice(0, 2));
            if ((dateInfo.wantsEvening || dateInfo.wantsNight) && hour < 6) {
                date = formatDate(addDays(parseDate(date), 1));
            }
        }

        const hasSignal = hasQuestionSignal(normalized)
            || channels.length > 0
            || allChannels
            || Boolean(time)
            || timeIsNow
            || dateExplicit;

        if (!hasSignal) {
            return { type: 'unknown', raw: original };
        }

        return {
            type: 'schedule',
            raw: original,
            channels: channels,
            allChannels: allChannels,
            time: time,
            date: date,
            dateExplicit: dateExplicit
        };
    }

    return {
        parseQuery: parseQuery,
        channels: CHANNELS,
        months: MONTHS
    };
});
