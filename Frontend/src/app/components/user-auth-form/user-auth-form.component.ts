import { Component, ElementRef, Input, signal, ViewChild, WritableSignal } from "@angular/core";
import { AuthFormEnum } from "../../../enums/auth-form.enum";
import { ButtonComponent } from "../button/button.component";
import { IconComponent } from "../icon/icon.component";
import { Router } from "@angular/router";
import { UserService } from "../../../services/user.service";
import { ApiResponse } from "../../../contracts/api/api.response";
import { SessionResponse } from "../../../contracts/session.response";

@Component({
  selector: "app-user-auth-form",
  imports: [ButtonComponent, IconComponent],
  templateUrl: "./user-auth-form.component.html",
  styleUrl: "./user-auth-form.component.css",
})
export class UserAuthFormComponent {
  @Input()
  public type: AuthFormEnum = AuthFormEnum.LOGIN;

  @ViewChild("userNameInput")
  private userNameInput?: ElementRef;

  @ViewChild("emailInput")
  private emailInput?: ElementRef;

  @ViewChild("passwordInput")
  private passwordInput?: ElementRef;

  protected readonly iconSize: number = 64;
  protected readonly AuthFormEnum = AuthFormEnum;

  protected readonly authResponse: WritableSignal<ApiResponse | null> = signal(null);

  constructor(
    private readonly router: Router,
    private readonly userService: UserService
  ) {}

  protected async handleSubmit(event: Event) {
    const submitEvent = event as SubmitEvent;
    submitEvent.preventDefault();

    const email = (this.emailInput?.nativeElement as HTMLInputElement).value;
    const password = (this.passwordInput?.nativeElement as HTMLInputElement).value;

    if (!email || !password) {
      return;
    }

    let response: ApiResponse | SessionResponse;
    switch (this.type) {
      case AuthFormEnum.LOGIN:
        response = await this.userService.loginUser(email, password);
        break;
      case AuthFormEnum.SIGNUP:
        const userName = (this.userNameInput?.nativeElement as HTMLInputElement).value;
        if (!userName) {
          return;
        }

        response = await this.userService.registerUser(userName, email, password);
        break;
    }

    if ("message" in response && "statusCode" in response) {
      this.authResponse.set(response);
      return;
    }

    localStorage.setItem("access_token", response.accessToken);
  }

  protected async goToOtherAuthForm() {
    const route = this.type == AuthFormEnum.LOGIN ? "/signup" : "/login";
    await this.router.navigateByUrl(route);
  }

  private handleAuth() {}
}
