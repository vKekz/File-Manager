import { Component, ElementRef, Input, ViewChild } from "@angular/core";
import { AuthFormEnum } from "../../../enums/auth-form.enum";
import { ButtonComponent } from "../button/button.component";
import { IconComponent } from "../icon/icon.component";
import { Router } from "@angular/router";
import { AuthController } from "../../../controllers/auth.controller";

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

  constructor(
    private readonly router: Router,
    protected readonly authController: AuthController
  ) {}

  protected async handleSubmit(event: Event) {
    const submitEvent = event as SubmitEvent;
    submitEvent.preventDefault();

    const email = (this.emailInput?.nativeElement as HTMLInputElement).value;
    const password = (this.passwordInput?.nativeElement as HTMLInputElement).value;

    switch (this.type) {
      case AuthFormEnum.LOGIN:
        await this.authController.loginUser(email, password);
        break;
      case AuthFormEnum.SIGNUP:
        const userName = (this.userNameInput?.nativeElement as HTMLInputElement).value;
        await this.authController.registerUser(userName, email, password);
        break;
    }
  }

  protected async goToOtherAuthForm() {
    this.authController.resetResponse();

    const route = this.type == AuthFormEnum.LOGIN ? "/signup" : "/login";
    await this.router.navigateByUrl(route);
  }
}
