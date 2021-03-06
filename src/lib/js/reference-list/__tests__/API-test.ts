import { getRemoteData, parseManualData } from '../API';

const testCSL = require('../../../../../scripts/fixtures.js').reflistState.CSL.aaaaaaaa;

describe('API', () => {
    describe('getRemoteData()', () => {
        let mce;
        beforeEach(() => {
            mce = { alert: jest.fn() };
        });
        it('should get a single PMID', () => {
            return getRemoteData('12345', <any>mce)
            .then(d => {
                expect(d[0].title).toBe('A new granulation method for compressed tablets [proceedings].');
            });
        });
        it('should get a single DOI', () => {
            return getRemoteData('10.1097/TA.0000000000000999', <any>mce)
            .then(d => {
                expect(d[0].title).toBe('Not all prehospital time is equal');
            });
        });
        it('should get a single PMCID', () => {
            return getRemoteData('PMC2837541', <any>mce)
            .then(d => {
                expect(d[0].title).toBe('Trauma Patients without a Trauma Diagnosis: The Data Gap');
            });
        });
        it('should get a combination of DOIs and PMIDs', () => {
            return getRemoteData('10.1097/TA.0000000000000999,12345', <any>mce)
            .then(d => {
                expect(d[0].title).toBe('A new granulation method for compressed tablets [proceedings].');
                expect(d[1].title).toBe('Not all prehospital time is equal');
            });
        });
        it('should error appropriately for invalid data', () => {
            return getRemoteData('10.1097/TA.0000000000000999,      a823hh,       12345', <any>mce)
            .then(d => {
                expect(mce.alert).toHaveBeenCalledTimes(1);
                expect(mce.alert).toHaveBeenCalledWith('Error: The following identifiers could not be found: a823hh'); // tslint:disable-line
                expect(d[0].title).toBe('A new granulation method for compressed tablets [proceedings].');
                expect(d[1].title).toBe('Not all prehospital time is equal');
            });
        });
        it('should error appropriately when no valid identifiers are set', () => {
            return getRemoteData(' sadfasfdg', <any>mce)
            .then(d => {
                expect(d.length).toBe(0);
                expect(mce.alert).toHaveBeenCalledTimes(1);
                expect(mce.alert).toHaveBeenCalledWith('No identifiers could be found for your request');
            });
        });
    });
    describe('parseManualData()', () => {
        let mockData;
        beforeEach(() => {
            mockData = Object.assign({}, {
                addManually: true,
                attachInline: true,
                identifierList: '',
                manualData: {...testCSL, issued: '2016/08/19'},
                people: [],
            });
        });
        it('should parse manual data', () => {
            const beforeDate = {'date-parts': [['2016', '08', '19']]};
            return parseManualData(mockData)
            .then(d => {
                expect(d[0].issued).toEqual(beforeDate);
            });
        });
        it('should drop empty fields', () => {
            mockData.manualData.abstract = '';
            expect(Object.keys(mockData.manualData).length).toBe(22);
            return parseManualData(mockData)
            .then(d => {
                expect(Object.keys(d[0]).length).toBe(21);
            });
        });
        it('should add "Person" types appropriately', () => {
            mockData.people = [
                {family: 'Smith', given: 'Robert', type: 'author'},
                {family: 'Brown', given: 'Susan', type: 'editor'},
            ];
            return parseManualData(mockData)
            .then(d => {
                expect(d[0].author.length).toBe(2);
                expect(d[0].editor.length).toBe(1);
            });
        });
        it('should generateId() if an ID does not already exists', () => {
            mockData.manualData.id = '';
            return parseManualData(mockData)
            .then(d => {
                expect(d[0].id).not.toBe('');
                expect(d[0].id).not.toBeUndefined();
            });
        });
    });
});
