declare module '../services/WebSocketService.js' {
  export default class WebSocketService {
    static init(user: any): void;
    static on(event: string, callback: (data: any) => void): void;
    static off(event: string, callback: (data: any) => void): void;
    static emit(event: string, data: any): void;
  }
}
